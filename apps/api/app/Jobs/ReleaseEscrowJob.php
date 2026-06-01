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
    public int $backoff = 60;

    public function __construct(private string $bookingId) {}

    public function handle(EscrowService $escrowService): void
    {
        $booking = Booking::with('escrow')->find($this->bookingId);

        if (!$booking) {
            Log::warning('ReleaseEscrowJob: booking not found', ['booking_id' => $this->bookingId]);
            return;
        }

        $escrow = $booking->escrow;
        if (!$escrow) {
            Log::warning('ReleaseEscrowJob: no escrow found', ['booking_id' => $this->bookingId]);
            return;
        }

        // Only auto-release if still in held state (not already released or in dispute)
        if (!in_array($escrow->status, ['held'])) {
            Log::info('ReleaseEscrowJob: skipping, escrow status is ' . $escrow->status, [
                'booking_id' => $this->bookingId,
            ]);
            return;
        }

        // Only release if booking is completed or closed
        if (!in_array($booking->status, ['completed', 'closed', 'confirmed', 'owner_notified', 'client_prepared', 'active'])) {
            Log::info('ReleaseEscrowJob: skipping, booking status is ' . $booking->status, [
                'booking_id' => $this->bookingId,
            ]);
            return;
        }

        try {
            $escrowService->release($booking, 'auto_24h');
            Log::info('ReleaseEscrowJob: escrow auto-released', ['booking_id' => $this->bookingId]);
        } catch (\Throwable $e) {
            Log::error('ReleaseEscrowJob: failed to release escrow', [
                'booking_id' => $this->bookingId,
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
