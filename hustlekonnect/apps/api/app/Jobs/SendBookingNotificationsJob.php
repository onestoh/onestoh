<?php
namespace App\Jobs;

use App\Models\Booking;
use App\Models\PlatformNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SendBookingNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public array $backoff = [30, 60, 120, 300, 600];

    public function __construct(private string $bookingId, private string $status) {}

    public function handle(): void
    {
        $booking = Booking::with(['user', 'asset', 'asset.yard.user'])->find($this->bookingId);
        if (!$booking) return;

        $client = $booking->user;
        $owner  = $booking->asset?->yard?->user;
        $asset  = $booking->asset?->title ?? 'Vehicle';
        $ref    = strtoupper(substr($this->bookingId, 0, 8));
        $start  = $booking->start_date?->format('d M Y') ?? 'N/A';
        $end    = $booking->end_date?->format('d M Y') ?? 'N/A';
        $amount = number_format($booking->total_amount_kes ?? 0);

        $messages = match ($this->status) {
            'pending_payment'  => [
                [$client, '🚗 Booking Created', "Your booking #{$ref} for {$asset} ({$start}–{$end}) needs payment of KES {$amount}. Complete within 15 minutes."],
            ],
            'confirmed' => [
                [$client, '✅ Booking Confirmed!', "Your booking #{$ref} for {$asset} is confirmed! KES {$amount} is in escrow."],
                [$owner,  '🔔 New Booking #{$ref}', "New confirmed booking for {$asset} from {$start} to {$end}. KES {$amount} in escrow. Please prepare the vehicle."],
            ],
            'owner_notified' => [
                [$owner,  '📋 Prepare Vehicle — #{$ref}', "Please ensure {$asset} is serviced and ready for pickup on {$start}."],
                [$client, '👍 Owner Notified', "The owner has been notified about your booking #{$ref} for {$asset}."],
            ],
            'client_prepared' => [
                [$client, '🚀 Pickup Tomorrow!', "Your {$asset} is ready! Pickup on {$start}. Bring your ID and reference #{$ref}."],
                [$owner,  '✔️ Client Notified', "Your client has been reminded about picking up {$asset} on {$start}."],
            ],
            'active' => [
                [$client, '🟢 Rental Active — #{$ref}', "Your rental of {$asset} is now active. Return by {$end}."],
                [$owner,  '🟢 Rental Started — #{$ref}', "{$asset} rental is active. Return expected: {$end}. Escrow secured."],
            ],
            'completed' => [
                [$client, '✅ Rental Complete', "Your rental of {$asset} is done. Escrow releases to owner in 24hr. Please leave a review!"],
                [$owner,  '🎉 Rental Complete — Payment Soon', "Rental #{$ref} complete! KES {$amount} releases to your wallet within 24 hours."],
            ],
            'closed' => [
                [$owner,  '💰 Payment Released', "KES {$amount} from booking #{$ref} has been credited to your wallet."],
                [$client, '🔒 Booking Closed', "Booking #{$ref} is fully closed. Thank you for using HustleKonnect!"],
            ],
            'cancelled' => [
                [$client, '❌ Booking Cancelled', "Your booking #{$ref} for {$asset} was cancelled. Any payments will be refunded within 3-5 business days."],
                [$owner,  '❌ Booking Cancelled', "Booking #{$ref} for {$asset} was cancelled. Your availability has been restored."],
            ],
            default => [],
        };

        foreach ($messages as [$user, $title, $body]) {
            if (!$user) continue;
            PlatformNotification::create([
                'id'      => (string) Str::uuid(),
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'booking_' . $this->status,
                'data'    => ['booking_id' => $this->bookingId],
            ]);
            // TODO: also send SMS via Africa's Talking + push via Firebase FCM
        }
    }
}
