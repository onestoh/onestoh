<?php
namespace App\Observers;

use App\Models\Booking;
use App\Jobs\SendBookingNotificationsJob;
use App\Jobs\AutoAdvanceBookingJob;
use App\Jobs\ReleaseEscrowJob;

class BookingObserver
{
    public function updated(Booking $booking): void
    {
        if (!$booking->isDirty('status')) return;

        $new = $booking->status;

        SendBookingNotificationsJob::dispatch($booking->id, $new)->onQueue('notifications');

        match ($new) {
            'confirmed'      => AutoAdvanceBookingJob::dispatch($booking->id, 'owner_notified', 'confirmed')->delay(now()->addMinutes(5))->onQueue('bookings'),
            'owner_notified' => AutoAdvanceBookingJob::dispatch($booking->id, 'client_prepared', 'owner_notified')->delay(now()->addSeconds(max(0, now()->diffInSeconds($booking->start_date) - 86400)))->onQueue('bookings'),
            'client_prepared'=> AutoAdvanceBookingJob::dispatch($booking->id, 'active', 'client_prepared')->delay(now()->addSeconds(max(0, now()->diffInSeconds($booking->start_date))))->onQueue('bookings'),
            'active'         => AutoAdvanceBookingJob::dispatch($booking->id, 'completed', 'active')->delay(now()->addSeconds(max(0, now()->diffInSeconds($booking->end_date))))->onQueue('bookings'),
            'completed'      => ReleaseEscrowJob::dispatch($booking->id)->delay(now()->addHours(24))->onQueue('payments'),
            default          => null,
        };
    }
}
