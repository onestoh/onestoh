<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\HotelRoom;
use App\Models\Property;
use App\Models\PropertyAvailability;
use App\Models\PropertyPricingRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Check if a property/room is available for a date range
     */
    public static function isAvailable(int $propertyId, string $checkIn, string $checkOut, ?int $roomId = null): bool
    {
        $dates = self::getDateRange($checkIn, $checkOut);
        return PropertyAvailability::where('property_id', $propertyId)
            ->whereIn('blocked_date', $dates)
            ->when($roomId, fn($q) => $q->whereHas('booking', fn($b) => $b->where('room_id', $roomId)))
            ->doesntExist();
    }

    /**
     * Get array of dates in range [checkIn, checkOut)
     */
    public static function getDateRange(string $checkIn, string $checkOut): array
    {
        $dates = [];
        $current = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);
        while ($current->lt($end)) {
            $dates[] = $current->toDateString();
            $current->addDay();
        }
        return $dates;
    }

    /**
     * Calculate total price for a stay
     */
    public static function calculatePrice(int $propertyId, string $checkIn, string $checkOut, ?int $roomId = null): array
    {
        $dates = self::getDateRange($checkIn, $checkOut);
        $nights = count($dates);
        $baseTotal = 0;

        $property = Property::find($propertyId);

        foreach ($dates as $date) {
            $rule = PropertyPricingRule::where('property_id', $propertyId)
                ->when($roomId, fn($q) => $q->where(fn($q2) => $q2->where('room_id', $roomId)->orWhereNull('room_id')))
                ->where(fn($q) => $q
                    ->where('day_of_week', Carbon::parse($date)->dayOfWeek)
                    ->orWhere(fn($q2) => $q2->where('date_from', '<=', $date)->where('date_to', '>=', $date))
                    ->orWhereNull('day_of_week')
                )
                ->orderByDesc('priority')
                ->first();

            if ($rule) {
                $baseTotal += $rule->price_per_night;
            } elseif ($roomId) {
                $room = HotelRoom::find($roomId);
                $baseTotal += $room ? $room->price_per_night : ($property->price ?? 0);
            } else {
                $baseTotal += $property->price ?? 0;
            }
        }

        $serviceFee  = round($baseTotal * 0.05, 2);
        $cleaningFee = $roomId ? 0 : 1500;
        $total       = $baseTotal + $serviceFee + $cleaningFee;

        return [
            'nights'       => $nights,
            'base_total'   => $baseTotal,
            'service_fee'  => $serviceFee,
            'cleaning_fee' => $cleaningFee,
            'total'        => $total,
        ];
    }

    /**
     * Create a booking — fully automated, no owner action needed
     */
    public static function create(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $pricing = self::calculatePrice(
                $data['property_id'],
                $data['check_in'],
                $data['check_out'],
                $data['room_id'] ?? null
            );

            $property = Property::findOrFail($data['property_id']);

            $booking = Booking::create([
                'property_id'          => $data['property_id'],
                'room_id'              => $data['room_id'] ?? null,
                'guest_id'             => $data['guest_id'],
                'host_id'              => $property->user_id,
                'type'                 => $data['type'],
                'check_in'             => $data['check_in'],
                'check_out'            => $data['check_out'],
                'guests_count'         => $data['guests_count'] ?? 1,
                'nights'               => $pricing['nights'],
                'total_price'          => $pricing['total'],
                'base_price_per_night' => $pricing['nights'] > 0 ? round($pricing['base_total'] / $pricing['nights'], 2) : 0,
                'cleaning_fee'         => $pricing['cleaning_fee'],
                'service_fee'          => $pricing['service_fee'],
                'status'               => 'pending',
                'special_requests'     => $data['special_requests'] ?? null,
                'auto_confirmed'       => true,
            ]);

            return $booking;
        });
    }

    /**
     * Confirm + pay a booking: block dates, notify host/guest, update status
     */
    public static function confirm(Booking $booking, string $paymentMethod, string $paymentRef): Booking
    {
        return DB::transaction(function () use ($booking, $paymentMethod, $paymentRef) {
            $booking->update([
                'status'           => 'confirmed',
                'payment_method'   => $paymentMethod,
                'payment_ref'      => $paymentRef,
                'paid_at'          => now(),
                'host_notified_at' => now(),
            ]);

            // Block all dates in range
            $dates = self::getDateRange($booking->check_in, $booking->check_out);
            foreach ($dates as $date) {
                PropertyAvailability::firstOrCreate(
                    ['property_id' => $booking->property_id, 'blocked_date' => $date],
                    ['reason' => 'booked', 'booking_id' => $booking->id]
                );
            }

            $guest    = \App\Models\User::find($booking->guest_id);
            $host     = \App\Models\User::find($booking->host_id);
            $property = \App\Models\Property::find($booking->property_id);
            $ref      = 'BOOK-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . '-' . now()->format('Ymd');

            try {
                // 1. Notify HOST — new booking received
                NotificationService::send(
                    $booking->host_id,
                    'New Booking Confirmed!',
                    "Booking #{$ref}: " . optional($guest)->name . " booked " . optional($property)->title . " · Check-in: {$booking->check_in} · Check-out: {$booking->check_out} · {$booking->nights} nights · KES " . number_format($booking->total_price) . " received via " . strtoupper($paymentMethod),
                    'payment',
                    '/dashboard/host-bookings'
                );

                // 2. Notify GUEST — booking confirmed
                NotificationService::send(
                    $booking->guest_id,
                    'Booking Confirmed!',
                    "Your booking at " . optional($property)->title . " is confirmed. Ref: {$ref}. Check-in: {$booking->check_in}. Check-out: {$booking->check_out}. Total paid: KES " . number_format($booking->total_price),
                    'payment',
                    '/dashboard/bookings'
                );

                // 3. Notify HOST — calendar updated automatically
                NotificationService::send(
                    $booking->host_id,
                    'Calendar Updated Automatically',
                    "Dates {$booking->check_in} to {$booking->check_out} have been blocked on your calendar. No action required.",
                    'system',
                    '/dashboard/host-bookings'
                );

                // 4. Send email (non-blocking)
                try {
                    \Illuminate\Support\Facades\Mail::queue(new \App\Mail\RentPaymentReceived(
                        \App\Models\RentPayment::make([
                            'amount'          => $booking->total_price,
                            'month_year'      => now()->format('Y-m'),
                            'payment_method'  => $paymentMethod,
                            'transaction_ref' => $paymentRef,
                            'status'          => 'paid',
                        ])
                    ));
                } catch (\Throwable $e) {}

            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('BookingService notify failed: ' . $e->getMessage());
            }

            return $booking->fresh();
        });
    }

    /**
     * Cancel a booking and unblock dates
     */
    public static function cancel(Booking $booking, string $reason = ''): Booking
    {
        PropertyAvailability::where('booking_id', $booking->id)->delete();
        $booking->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at'        => now(),
        ]);
        return $booking->fresh();
    }

    /**
     * Get blocked dates for a property as array of date strings
     */
    public static function getBlockedDates(int $propertyId, ?int $roomId = null, int $months = 3): array
    {
        return PropertyAvailability::where('property_id', $propertyId)
            ->where('blocked_date', '>=', now()->toDateString())
            ->where('blocked_date', '<=', now()->addMonths($months)->toDateString())
            ->pluck('blocked_date')
            ->toArray();
    }
}
