<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\FraudFlag;
use App\Models\FraudRiskProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class FraudDetectionService
{
    public function assessBookingRisk(Booking $booking, Request $request): float
    {
        $score = 0.0;
        $ip    = $request->ip();

        // 1. IP velocity — bookings from same IP in last hour
        $ipCount = Booking::whereHas('renter', function ($q) use ($ip) {
            // proxy via search events or store IP on booking if available
        })->count();

        $recentBookingsSameIp = DB::table('bookings')
            ->where('renter_ip', $ip)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentBookingsSameIp >= 3) $score += 25;
        elseif ($recentBookingsSameIp >= 2) $score += 10;

        // 2. Account age
        $renter = $booking->renter ?? User::find($booking->renter_id);
        if ($renter) {
            $ageDays = $renter->created_at->diffInDays(now());
            if ($ageDays < 1)  $score += 30;
            elseif ($ageDays < 7)  $score += 15;
            elseif ($ageDays < 30) $score += 5;

            // 3. KYC completeness
            $kycComplete = !empty($renter->id_verified_at);
            if (!$kycComplete) $score += 15;

            // 4. Booking value vs user history
            $avgPastBooking = Booking::where('renter_id', $renter->id)
                ->where('status', 'closed')
                ->avg('total_amount');

            if ($avgPastBooking && $booking->total_amount > $avgPastBooking * 3) {
                $score += 20;
            }

            // 5. Existing risk profile
            $profile = FraudRiskProfile::where('user_id', $renter->id)->first();
            if ($profile) {
                $score += $profile->overall_risk_score * 0.2;
            }
        }

        return min(100, round($score, 2));
    }

    public function assessPaymentRisk(array $paymentData, Request $request): float
    {
        $score = 0.0;
        $ip    = $request->ip();

        // Card testing: many small amounts from same IP in 10 minutes
        $smallAmountCount = DB::table('payments')
            ->where('payer_ip', $ip)
            ->where('amount', '<', 100)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($smallAmountCount >= 5) $score += 40;

        // Amount anomaly: unusually large single payment
        $avgPayment = DB::table('payments')->avg('amount');
        if ($avgPayment && isset($paymentData['amount']) && $paymentData['amount'] > $avgPayment * 5) {
            $score += 25;
        }

        // Geographic mismatch (card country vs IP country)
        if (isset($paymentData['card_country'], $paymentData['ip_country'])
            && $paymentData['card_country'] !== $paymentData['ip_country']) {
            $score += 20;
        }

        return min(100, round($score, 2));
    }

    public function assessKycRisk(User $user, array $documents): float
    {
        $score = 0.0;

        // Rapid resubmission: KYC attempts in last 24h
        $recentAttempts = DB::table('kyc_submissions')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($recentAttempts >= 3) $score += 30;
        elseif ($recentAttempts >= 2) $score += 15;

        // Phone number reuse across accounts
        if ($user->phone) {
            $phoneCount = User::where('phone', $user->phone)
                ->where('id', '!=', $user->id)
                ->count();
            if ($phoneCount >= 2) $score += 35;
            elseif ($phoneCount === 1) $score += 20;
        }

        // Duplicate document hash check
        foreach ($documents as $doc) {
            if (isset($doc['hash'])) {
                $dupCount = DB::table('kyc_submissions')
                    ->where('document_hash', $doc['hash'])
                    ->where('user_id', '!=', $user->id)
                    ->count();
                if ($dupCount > 0) {
                    $score += 40;
                }
            }
        }

        return min(100, round($score, 2));
    }

    public function flagUser(User $user, string $flagType, float $riskScore, array $evidence): FraudFlag
    {
        $flag = FraudFlag::create([
            'user_id'    => $user->id,
            'ip_address' => request()->ip(),
            'flag_type'  => $flagType,
            'risk_score' => $riskScore,
            'evidence'   => $evidence,
            'details'    => "Auto-flagged: {$flagType} (score {$riskScore})",
            'status'     => 'open',
        ]);

        // Update risk profile
        $this->updateRiskProfile($user);

        // Auto-escalate high risk
        if ($riskScore >= 75) {
            $this->notifyAdmins($flag);
        }

        return $flag;
    }

    public function updateRiskProfile(User $user): FraudRiskProfile
    {
        $flags          = FraudFlag::where('user_id', $user->id)->get();
        $flagsCount     = $flags->count();
        $confirmedCount = $flags->where('status', 'confirmed_fraud')->count();
        $openFlags      = $flags->whereIn('status', ['open', 'investigating']);

        // Weighted score: confirmed fraud is most impactful
        $score = 0;
        $score += $confirmedCount * 40;
        $score += $openFlags->where('risk_score', '>=', 75)->count() * 20;
        $score += $openFlags->where('risk_score', '>=', 40)->where('risk_score', '<', 75)->count() * 10;
        $score  = min(100, $score);

        $riskFactors = [];
        foreach ($openFlags->take(5) as $flag) {
            $riskFactors[] = ['type' => $flag->flag_type, 'score' => $flag->risk_score, 'date' => $flag->created_at->toDateString()];
        }

        $profile = FraudRiskProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'overall_risk_score'   => $score,
                'flags_count'          => $flagsCount,
                'confirmed_fraud_count'=> $confirmedCount,
                'last_known_ip'        => request()->ip(),
                'risk_factors'         => $riskFactors,
                'last_assessed_at'     => now(),
            ]
        );

        return $profile;
    }

    public function getHighRiskQueue(): Collection
    {
        return FraudFlag::with(['user', 'reviewer'])
            ->whereIn('status', ['open', 'investigating'])
            ->where('risk_score', '>=', 50)
            ->orderByDesc('risk_score')
            ->get();
    }

    private function notifyAdmins(FraudFlag $flag): void
    {
        // Dispatch admin notification — implementation depends on notification class
        // \Notification::route('mail', config('mail.admin'))->notify(new HighRiskFlagNotification($flag));
        \Illuminate\Support\Facades\Log::warning('High risk fraud flag', ['flag_id' => $flag->id, 'user_id' => $flag->user_id, 'score' => $flag->risk_score]);
    }
}
