<?php
namespace App\Observers;

use App\Models\Booking;
use App\Jobs\SendBookingNotificationsJob;
use App\Jobs\ReleaseEscrowJob;
use App\Jobs\AutoAdvanceBookingJob;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    public function updated(Booking $booking): void
    {
        if (!$booking->isDirty('status')) {
            return;
        }

        $oldStatus = $booking->getOriginal('status');
        $newStatus = $booking->status;

        Log::info("Booking status transition", [
            'booking_id' => $booking->id,
            'from' => $oldStatus,
            'to' => $newStatus,
        ]);

        // Fire notification job for every transition
        SendBookingNotificationsJob::dispatch($booking->id, $newStatus)->onQueue('notifications');

        // Automation: schedule next stage advancement
        match ($newStatus) {
            'confirmed' => $this->scheduleOwnerPreparation($booking),
            'owner_notified' => $this->scheduleClientPreparation($booking),
            'client_prepared' => $this->scheduleActivation($booking),
            'active' => $this->scheduleCompletion($booking),
            'completed' => $this->triggerEscrowRelease($booking),
            default => null,
        };
    }

    private function scheduleOwnerPreparation(Booking $booking): void
    {
        // Auto-advance to owner_notified after 5 min if not already done
        AutoAdvanceBookingJob::dispatch($booking->id, 'owner_notified', 'confirmed')
            ->delay(now()->addMinutes(5))
            ->onQueue('bookings');
    }

    private function scheduleClientPreparation(Booking $booking): void
    {
        // Auto-advance to client_prepared 24 hours before start
        $delay = max(0, now()->diffInSeconds($booking->start_date) - 86400);
        AutoAdvanceBookingJob::dispatch($booking->id, 'client_prepared', 'owner_notified')
            ->delay(now()->addSeconds((int)$delay))
            ->onQueue('bookings');
    }

    private function scheduleActivation(Booking $booking): void
    {
        // Auto-activate at booking start_date
        $delay = max(0, now()->diffInSeconds($booking->start_date));
        AutoAdvanceBookingJob::dispatch($booking->id, 'active', 'client_prepared')
            ->delay(now()->addSeconds((int)$delay))
            ->onQueue('bookings');
    }

    private function scheduleCompletion(Booking $booking): void
    {
        // Auto-complete at booking end_date
        $delay = max(0, now()->diffInSeconds($booking->end_date));
        AutoAdvanceBookingJob::dispatch($booking->id, 'completed', 'active')
            ->delay(now()->addSeconds((int)$delay))
            ->onQueue('bookings');
    }

    private function triggerEscrowRelease(Booking $booking): void
    {
        // Auto-release escrow 24 hours after completion (gives time for disputes)
        ReleaseEscrowJob::dispatch($booking->id)
            ->delay(now()->addHours(24))
            ->onQueue('payments');
    }
}
