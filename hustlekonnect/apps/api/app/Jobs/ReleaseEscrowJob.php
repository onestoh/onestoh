<?php
namespace App\Jobs;

use App\Models\Booking;
use App\Services\EscrowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReleaseEscrowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [60, 300, 600];

    public function __construct(private string $bookingId) {}

    public function handle(EscrowService $escrow): void
    {
        $booking = Booking::find($this->bookingId);
        if (!$booking || $booking->status !== 'completed') {
            Log::info("ReleaseEscrowJob skipped: booking {$this->bookingId} status={$booking?->status}");
            return;
        }

        if ($booking->hasOpenDispute()) {
            Log::info("ReleaseEscrowJob deferred: open dispute on {$this->bookingId}");
            self::dispatch($this->bookingId)->delay(now()->addHours(24))->onQueue('payments');
            return;
        }

        $escrow->release($booking);
    }
}
