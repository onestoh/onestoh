<?php
namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoAdvanceBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private string $bookingId,
        private string $targetStatus,
        private string $requiredCurrentStatus
    ) {}

    public function handle(): void
    {
        $booking = Booking::find($this->bookingId);

        if (!$booking) {
            return;
        }

        // Only advance if still in the expected status
        if ($booking->status !== $this->requiredCurrentStatus) {
            Log::info("AutoAdvanceBookingJob: booking {$this->bookingId} already at {$booking->status}, skip");
            return;
        }

        // Don't advance if there's an open dispute
        if ($booking->disputes()->where('status', 'open')->exists()) {
            Log::info("AutoAdvanceBookingJob: booking {$this->bookingId} has open dispute, skip");
            return;
        }

        $booking->update(['status' => $this->targetStatus]);

        Log::info("AutoAdvanceBookingJob: advanced booking {$this->bookingId} to {$this->targetStatus}");
    }
}
