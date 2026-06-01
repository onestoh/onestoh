<?php
namespace App\Jobs;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookingNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 30;

    public function __construct(
        private string $bookingId,
        private string $eventType,
        private array $extraData = [],
    ) {}

    public function handle(NotificationService $notifications): void
    {
        $booking = Booking::with(['asset.owner', 'client', 'broker'])->find($this->bookingId);

        if (!$booking) {
            Log::warning('SendBookingNotificationsJob: booking not found', ['booking_id' => $this->bookingId]);
            return;
        }

        $data = array_merge([
            'booking_id' => $booking->id,
            'asset_name' => "{$booking->asset->make} {$booking->asset->model}",
            'start_date' => $booking->start_at->format('d M Y H:i'),
            'end_date'   => $booking->end_at->format('d M Y H:i'),
            'amount'     => number_format($booking->total_amount, 2),
        ], $this->extraData);

        // Notify client
        $notifications->send($booking->client, $this->eventType, $data);

        // Notify asset owner
        if ($booking->asset->owner) {
            $notifications->send($booking->asset->owner, $this->eventType, $data);
        }

        // Notify broker if assigned
        if ($booking->broker_id && $booking->broker) {
            $notifications->send($booking->broker, $this->eventType, $data);
        }

        // Notify driver if assigned
        if ($booking->driver_id && $booking->driver) {
            $notifications->send($booking->driver, $this->eventType, $data);
        }

        Log::info('SendBookingNotificationsJob: dispatched', [
            'booking_id' => $this->bookingId,
            'event'      => $this->eventType,
        ]);
    }
}
