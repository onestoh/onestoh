<?php
namespace App\Services;

use App\Models\{User, YardGroupSsoToken, YardGroupCrossReferral};
use Illuminate\Support\Facades\{Cache, Http};
use Illuminate\Support\Str;

class YardGroupSsoService
{
    /**
     * Generate a short-lived SSO token stored in Redis (5-min TTL).
     */
    public function generateSsoToken(User $user, string $targetPlatform): string
    {
        $token = Str::random(64);

        Cache::put("yardgroup:sso:{$token}", [
            'user_id'         => $user->id,
            'target_platform' => $targetPlatform,
            'email'           => $user->email,
            'name'            => $user->name,
        ], 300); // 5 minutes

        // Also persist to DB for audit trail
        YardGroupSsoToken::create([
            'user_id'    => $user->id,
            'platform'   => $targetPlatform,
            'sso_token'  => $token,
            'shared_profile' => [
                'name'           => $user->name,
                'email'          => $user->email,
                'phone'          => $user->phone,
                'kyc_verified'   => $user->kyc_verified ?? false,
            ],
            'expires_at' => now()->addMinutes(5),
        ]);

        return $token;
    }

    /**
     * Validate an incoming SSO token (one-time use).
     * Returns the local User if found, or null.
     */
    public function validateSsoToken(string $token, string $platform): ?User
    {
        $data = Cache::pull("yardgroup:sso:{$token}");

        if (!$data) {
            // Check DB for non-expired token
            $record = YardGroupSsoToken::where('sso_token', $token)
                ->where('platform', $platform)
                ->where('expires_at', '>', now())
                ->first();

            if (!$record) {
                return null;
            }
            $data = ['user_id' => $record->user_id];

            // Invalidate
            $record->update(['expires_at' => now()]);
        }

        return User::find($data['user_id']);
    }

    /**
     * Persist a cross-platform account link.
     */
    public function linkPlatformAccount(User $user, string $platform, string $platformUserId): void
    {
        YardGroupSsoToken::updateOrCreate(
            ['user_id' => $user->id, 'platform' => $platform],
            [
                'platform_user_id' => $platformUserId,
                'sso_token'        => Str::random(64),
                'expires_at'       => now()->addYears(10),
            ]
        );
    }

    /**
     * Return cross-platform referral stats for a broker.
     */
    public function getCrossReferralStats(User $broker): array
    {
        $referrals = YardGroupCrossReferral::where('broker_id', $broker->id)->get();

        return [
            'total_clicks'      => $referrals->where('status', 'clicked')->count(),
            'total_registrations' => $referrals->whereIn('status', ['registered', 'transacted', 'commission_paid'])->count(),
            'total_transactions'  => $referrals->whereIn('status', ['transacted', 'commission_paid'])->count(),
            'total_commission'    => $referrals->sum('commission_earned'),
            'by_platform'         => $referrals->groupBy('target_platform')->map(fn ($group) => [
                'clicks'       => $group->where('status', 'clicked')->count(),
                'registrations' => $group->whereIn('status', ['registered', 'transacted', 'commission_paid'])->count(),
                'commission'   => $group->sum('commission_earned'),
            ]),
        ];
    }

    /**
     * Build a unified YardGroup profile for the user.
     */
    public function getUnifiedProfile(User $user): array
    {
        $profile = [
            'user_id'    => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'platforms'  => ['theonlineyard'],
            'toy_wallet_balance' => $user->wallet_balance ?? 0,
            'toy_bookings_count' => $user->bookingsAsRenter()->count(),
        ];

        // Attempt to fetch from EstateYard
        $estateToken = $this->generateSsoToken($user, 'estateyard');
        try {
            $estateResp = Http::withHeaders(['X-SSO-Token' => $estateToken])
                ->timeout(3)
                ->get(config('services.yardgroup.estateyard_api') . '/api/v1/yardgroup/sso/validate');
            if ($estateResp->successful()) {
                $profile['platforms'][]        = 'estateyard';
                $profile['estate_properties']  = $estateResp->json('properties_count', 0);
                $profile['estate_wallet']      = $estateResp->json('wallet_balance', 0);
            }
        } catch (\Throwable) {}

        // Attempt to fetch from MotorYard
        $motorToken = $this->generateSsoToken($user, 'motoryard');
        try {
            $motorResp = Http::withHeaders(['X-SSO-Token' => $motorToken])
                ->timeout(3)
                ->get(config('services.yardgroup.motoryard_api') . '/api/v1/yardgroup/sso/validate');
            if ($motorResp->successful()) {
                $profile['platforms'][]       = 'motoryard';
                $profile['motor_vehicles']    = $motorResp->json('vehicles_count', 0);
                $profile['motor_wallet']      = $motorResp->json('wallet_balance', 0);
            }
        } catch (\Throwable) {}

        return $profile;
    }
}
