<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function create(User $client, array $data): Booking
    {
        $asset = Asset::findOrFail($data['asset_id']);

        if (!$asset->isAvailableFor($data['start_date'], $data['end_date'])) {
            throw new \RuntimeException('Asset not available for selected dates.');
        }

        $days = now()->parse($data['start_date'])->diffInDays($data['end_date']);
        $baseAmount = $days * $asset->daily_rate;
        $driverFee  = ($data['with_driver'] ?? false) ? ($days * ($asset->driver_daily_rate ?? 0)) : 0;
        $platformFee = $baseAmount * 0.05;
        $total = $baseAmount + $driverFee + $platformFee;

        return DB::transaction(function () use ($client, $data, $asset, $days, $baseAmount, $driverFee, $platformFee, $total) {
            return Booking::create([
                'id'              => (string) Str::uuid(),
                'user_id'         => $client->id,
                'asset_id'        => $asset->id,
                'yard_id'         => $asset->yard_id,
                'start_date'      => $data['start_date'],
                'end_date'        => $data['end_date'],
                'status'          => 'pending_payment',
                'with_driver'     => $data['with_driver'] ?? false,
                'insurance_type'  => $data['insurance_type'] ?? 'none',
                'pickup_location' => $data['pickup_location'] ?? null,
                'notes'           => $data['notes'] ?? null,
                'currency'        => $data['currency'] ?? $client->preferred_currency ?? 'KES',
                'rental_days'     => $days,
                'base_amount_kes' => $baseAmount,
                'driver_fee_kes'  => $driverFee,
                'platform_fee_kes'=> $platformFee,
                'total_amount_kes'=> $total,
            ]);
        });
    }

    public function cancel(Booking $booking, string $reason = ''): void
    {
        if (in_array($booking->status, ['completed', 'closed', 'cancelled'])) {
            throw new \RuntimeException('Cannot cancel a booking in status: ' . $booking->status);
        }

        $booking->update([
            'status'            => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at'      => now(),
        ]);
    }
}
