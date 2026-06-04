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
        if (!$booking || $booking->status !== $this->requiredCurrentStatus) return;
        if ($booking->hasOpenDispute()) {
            Log::info("AutoAdvance skipped: open dispute on {$this->bookingId}");
            return;
        }
        $booking->update(['status' => $this->targetStatus]);
        Log::info("AutoAdvance: {$this->bookingId} → {$this->targetStatus}");
    }
}
