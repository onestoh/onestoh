<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\ReferralClick;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    const COMMISSION_RATE = 0.05; // 5% of base rental fee

    /**
     * Record a referral link click.
     */
    public function recordClick(string $referralCode, ?int $assetId, Request $request): void
    {
        $broker = User::where('referral_code', $referralCode)
            ->where('role', 'broker')
            ->first();

        if (!$broker) {
            Log::debug('Referral click with unknown code', ['code' => $referralCode]);
            return;
        }

        ReferralClick::create([
            'referral_code' => $referralCode,
            'broker_id'     => $broker->id,
            'asset_id'      => $assetId,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);
    }

    /**
     * Mark a referral click as converted (booking created).
     */
    public function markConverted(string $referralCode, string $bookingId): void
    {
        ReferralClick::where('referral_code', $referralCode)
            ->whereNull('converted_booking_id')
            ->latest()
            ->first()
            ?->update([
                'converted_booking_id' => $bookingId,
                'converted_at'         => now(),
            ]);
    }

    /**
     * Credit broker commission after escrow release.
     */
    public function creditBrokerCommission(Booking $booking): void
    {
        if (!$booking->broker_id || $booking->broker_commission <= 0) return;

        DB::transaction(function () use ($booking) {
            $broker = $booking->broker;
            $wallet = $broker->wallet ?? Wallet::create(['user_id' => $broker->id]);

            $wallet->credit(
                $booking->broker_commission,
                'commission',
                $booking->id,
                "Broker commission for booking #{$booking->id}"
            );

            Log::info('Broker commission credited', [
                'broker_id'  => $broker->id,
                'booking_id' => $booking->id,
                'amount'     => $booking->broker_commission,
            ]);
        });
    }

    /**
     * Get broker statistics for a given period.
     */
    public function getBrokerStats(User $broker, string $from, string $to): array
    {
        $clicks = ReferralClick::where('broker_id', $broker->id)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $conversions = ReferralClick::where('broker_id', $broker->id)
            ->whereBetween('converted_at', [$from, $to])
            ->whereNotNull('converted_booking_id')
            ->count();

        $commissions = Booking::where('broker_id', $broker->id)
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['completed', 'closed'])
            ->sum('broker_commission');

        $conversionRate = $clicks > 0 ? round(($conversions / $clicks) * 100, 1) : 0;

        return [
            'clicks'          => $clicks,
            'conversions'     => $conversions,
            'conversion_rate' => $conversionRate,
            'total_commission'=> $commissions,
            'pending_bookings'=> Booking::where('broker_id', $broker->id)
                ->whereNotIn('status', ['completed', 'closed', 'cancelled_by_client', 'cancelled_by_admin', 'cancelled_by_system'])
                ->count(),
        ];
    }

    /**
     * Get commission history for a broker.
     */
    public function getCommissionHistory(User $broker, int $perPage = 20)
    {
        return Booking::where('broker_id', $broker->id)
            ->whereIn('status', ['completed', 'closed'])
            ->with(['asset:id,make,model,category', 'client:id,name'])
            ->select(['id', 'asset_id', 'client_id', 'broker_commission', 'base_amount', 'status', 'created_at'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Validate referral code and return broker if valid.
     */
    public function validateCode(string $code): ?User
    {
        return User::where('referral_code', $code)
            ->where('role', 'broker')
            ->where('kyc_status', 'approved')
            ->first();
    }
}
