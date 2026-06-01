<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\HeavyEquipmentDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class HeavyEquipmentService
{
    private const EARTH_RADIUS_KM = 6371;
    private const PER_KM_RATE     = 150; // KES per km for mobilisation

    /**
     * Calculate mobilisation cost using the Haversine formula.
     */
    public function calculateMobilisationCost(Asset $asset, float $siteLat, float $siteLng): float
    {
        $detail = HeavyEquipmentDetail::where('asset_id', $asset->id)->firstOrFail();

        // Use asset yard location as origin
        $originLat = (float) optional($asset->yard)->latitude  ?? 0;
        $originLng = (float) optional($asset->yard)->longitude ?? 0;

        $dLat = deg2rad($siteLat - $originLat);
        $dLng = deg2rad($siteLng - $originLng);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($originLat)) * cos(deg2rad($siteLat)) * sin($dLng / 2) ** 2;

        $distanceKm = 2 * self::EARTH_RADIUS_KM * asin(sqrt($a));

        // Use detail's mobilisation_cost as flat rate if non-zero, else per-km
        if ((float) $detail->mobilisation_cost > 0) {
            return round((float) $detail->mobilisation_cost, 2);
        }

        return round($distanceKm * self::PER_KM_RATE, 2);
    }

    /**
     * Create a project booking for heavy equipment, with owner review gate if required.
     */
    public function createProjectBooking(array $data, User $client): Booking
    {
        $asset  = Asset::findOrFail($data['asset_id']);
        $detail = HeavyEquipmentDetail::where('asset_id', $asset->id)->first();

        $status = ($detail && $detail->owner_review_required)
            ? 'pending_owner_review'
            : 'pending_payment';

        $mobilisationCost = isset($data['site_latitude'], $data['site_longitude'])
            ? $this->calculateMobilisationCost($asset, (float) $data['site_latitude'], (float) $data['site_longitude'])
            : (float) ($detail->mobilisation_cost ?? 0);

        $booking = Booking::create(array_merge($data, [
            'client_id'        => $client->id,
            'status'           => $status,
            'mobilisation_cost'=> $mobilisationCost,
        ]));

        if ($status === 'pending_owner_review') {
            $asset->owner->notify(new \App\Notifications\ProjectBookingReviewNotification($booking));
        }

        return $booking;
    }

    /**
     * Owner approves a project booking and moves it to payment processing.
     */
    public function ownerApproveBooking(Booking $booking, User $owner): void
    {
        abort_if($booking->asset->owner_id !== $owner->id, 403, 'Not your asset.');

        $booking->update([
            'owner_approved'     => true,
            'owner_reviewed_at'  => now(),
            'status'             => 'pending_payment',
        ]);

        $booking->client->notify(new \App\Notifications\BookingApprovedNotification($booking));
    }

    /**
     * Owner declines the booking and issues a refund if payment was already made.
     */
    public function ownerDeclineBooking(Booking $booking, User $owner, string $reason): void
    {
        abort_if($booking->asset->owner_id !== $owner->id, 403, 'Not your asset.');

        $booking->update([
            'owner_approved'    => false,
            'owner_reviewed_at' => now(),
            'status'            => 'cancelled',
        ]);

        // Trigger refund if payment exists
        if ($booking->payments()->where('status', 'paid')->exists()) {
            app(PaymentService::class)->refundBooking($booking, $reason);
        }

        $booking->client->notify(new \App\Notifications\BookingDeclinedNotification($booking, $reason));
    }

    /**
     * Calculate early termination fee based on remaining hire days.
     */
    public function calculateEarlyTerminationFee(Booking $booking, Carbon $terminationDate): float
    {
        $detail = HeavyEquipmentDetail::where('asset_id', $booking->asset_id)->firstOrFail();

        $originalEnd   = Carbon::parse($booking->ends_at);
        $remainingDays = max(0, $terminationDate->diffInDays($originalEnd, false));

        $dailyRate     = (float) $booking->total_price / max(1, Carbon::parse($booking->starts_at)->diffInDays($originalEnd));
        $feePct        = (float) $detail->early_termination_fee_pct / 100;

        return round($remainingDays * $dailyRate * $feePct, 2);
    }
}
