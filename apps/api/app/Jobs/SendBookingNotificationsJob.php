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
    public array $backoff = [30, 60, 120, 300, 600];

    public function __construct(
        private string $bookingId,
        private string $status
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        $booking = Booking::with([
            'user',
            'asset',
            'asset.yard',
            'asset.yard.user',
            'driver',
        ])->find($this->bookingId);

        if (!$booking) {
            return;
        }

        $client = $booking->user;
        $owner = $booking->asset?->yard?->user;
        $assetName = $booking->asset?->title ?? 'Vehicle';
        $bookingRef = strtoupper(substr($booking->id, 0, 8));
        $startDate = $booking->start_date ? date('d M Y', strtotime($booking->start_date)) : 'N/A';
        $endDate = $booking->end_date ? date('d M Y', strtotime($booking->end_date)) : 'N/A';
        $amount = number_format($booking->total_amount_kes ?? 0);

        match ($this->status) {
            'pending_payment' => $this->notifyPendingPayment($notificationService, $client, $owner, $assetName, $bookingRef, $startDate, $endDate, $amount, $booking),
            'payment_processing' => $this->notifyPaymentProcessing($notificationService, $client, $owner, $assetName, $bookingRef, $amount),
            'confirmed' => $this->notifyConfirmed($notificationService, $client, $owner, $assetName, $bookingRef, $startDate, $endDate, $amount, $booking),
            'owner_notified' => $this->notifyOwnerNotified($notificationService, $client, $owner, $assetName, $bookingRef, $startDate),
            'client_prepared' => $this->notifyClientPrepared($notificationService, $client, $owner, $assetName, $bookingRef, $startDate, $booking),
            'active' => $this->notifyActive($notificationService, $client, $owner, $assetName, $bookingRef, $endDate, $booking),
            'completed' => $this->notifyCompleted($notificationService, $client, $owner, $assetName, $bookingRef, $amount, $booking),
            'closed' => $this->notifyClosed($notificationService, $client, $owner, $assetName, $bookingRef, $amount),
            'cancelled' => $this->notifyCancelled($notificationService, $client, $owner, $assetName, $bookingRef, $booking),
            default => Log::info("No notification template for status: {$this->status}"),
        };
    }

    private function notifyPendingPayment($ns, $client, $owner, $asset, $ref, $start, $end, $amount, $booking): void
    {
        if ($client) {
            $ns->sendToUser($client,
                "Booking Created — Payment Required",
                "Your booking #{$ref} for {$asset} ({$start} – {$end}) is pending payment of KES {$amount}. Complete payment within 15 minutes to secure your slot.",
                ['type' => 'booking_pending_payment', 'booking_id' => $booking->id]
            );
        }
    }

    private function notifyPaymentProcessing($ns, $client, $owner, $asset, $ref, $amount): void
    {
        if ($client) {
            $ns->sendToUser($client,
                "Payment Processing",
                "We've received your payment request of KES {$amount} for booking #{$ref}. Please complete the M-Pesa prompt on your phone.",
                ['type' => 'booking_payment_processing']
            );
        }
    }

    private function notifyConfirmed($ns, $client, $owner, $asset, $ref, $start, $end, $amount, $booking): void
    {
        // Notify CLIENT
        if ($client) {
            $ns->sendToUser($client,
                "Booking Confirmed! #{$ref}",
                "Great news! Your booking for {$asset} ({$start} – {$end}) is confirmed. KES {$amount} is held in escrow. The owner has been notified.",
                ['type' => 'booking_confirmed', 'booking_id' => $booking->id]
            );
        }

        // Notify OWNER — critical for automation awareness
        if ($owner) {
            $ns->sendToUser($owner,
                "New Confirmed Booking! #{$ref}",
                "You have a new confirmed booking for {$asset} from {$start} to {$end}. KES {$amount} is in escrow. Please prepare the vehicle for handover.",
                ['type' => 'booking_confirmed_owner', 'booking_id' => $booking->id]
            );
        }
    }

    private function notifyOwnerNotified($ns, $client, $owner, $asset, $ref, $start): void
    {
        if ($owner) {
            $ns->sendToUser($owner,
                "Booking Action Required — #{$ref}",
                "Reminder: Your {$asset} is booked from {$start}. Please ensure it is serviced, clean, and ready for pickup. The client will be notified 24 hours before.",
                ['type' => 'booking_owner_preparation', 'status' => 'owner_notified']
            );
        }

        if ($client) {
            $ns->sendToUser($client,
                "Owner Notified — #{$ref}",
                "The vehicle owner has been notified of your booking for {$asset}. You'll receive preparation details 24 hours before {$start}.",
                ['type' => 'booking_owner_notified_client']
            );
        }
    }

    private function notifyClientPrepared($ns, $client, $owner, $asset, $ref, $start, $booking): void
    {
        $pickup = $booking->pickup_location ?? 'the agreed location';

        if ($client) {
            $ns->sendToUser($client,
                "Your Booking Starts Tomorrow — #{$ref}",
                "{$asset} is ready for you! Pickup at {$pickup} on {$start}. Bring your ID and the booking reference #{$ref}.",
                ['type' => 'booking_client_prepared', 'booking_id' => $booking->id]
            );
        }

        if ($owner) {
            $ns->sendToUser($owner,
                "Client Notified — Pickup Tomorrow",
                "Your client has been reminded about their {$asset} pickup tomorrow ({$start}) at {$pickup}.",
                ['type' => 'booking_client_prepared_owner']
            );
        }
    }

    private function notifyActive($ns, $client, $owner, $asset, $ref, $end, $booking): void
    {
        if ($client) {
            $ns->sendToUser($client,
                "Booking Active — #{$ref}",
                "Your rental of {$asset} is now active. Return by {$end}. For emergencies, contact support immediately.",
                ['type' => 'booking_active', 'booking_id' => $booking->id]
            );
        }

        if ($owner) {
            $ns->sendToUser($owner,
                "{$asset} Rental Started — #{$ref}",
                "Your vehicle {$asset} rental is now active. Client has taken possession. Return expected: {$end}. Escrow funds are secured.",
                ['type' => 'booking_active_owner', 'booking_id' => $booking->id]
            );
        }

        // If there's a driver assigned
        if ($booking->driver) {
            $ns->sendToUser($booking->driver->user ?? null,
                "Assignment Active — #{$ref}",
                "Your driving assignment for {$asset} is now active. Safe driving!",
                ['type' => 'booking_active_driver']
            );
        }
    }

    private function notifyCompleted($ns, $client, $owner, $asset, $ref, $amount, $booking): void
    {
        if ($client) {
            $ns->sendToUser($client,
                "Booking Completed — #{$ref}",
                "Your rental of {$asset} has ended. We hope you had a great experience! Escrow will be released to the owner in 24 hours. Please leave a review.",
                ['type' => 'booking_completed', 'booking_id' => $booking->id]
            );
        }

        if ($owner) {
            $ns->sendToUser($owner,
                "Rental Completed — Payment Releasing Soon",
                "Your {$asset} rental (#{$ref}) is complete! KES {$amount} will be released to your wallet within 24 hours after dispute window. Rate your client to improve platform trust.",
                ['type' => 'booking_completed_owner', 'booking_id' => $booking->id]
            );
        }
    }

    private function notifyClosed($ns, $client, $owner, $asset, $ref, $amount): void
    {
        if ($owner) {
            $ns->sendToUser($owner,
                "Payment Released to Your Wallet — #{$ref}",
                "KES {$amount} from booking #{$ref} ({$asset}) has been released to your wallet. You can withdraw at any time.",
                ['type' => 'booking_closed_owner_paid']
            );
        }

        if ($client) {
            $ns->sendToUser($client,
                "Booking Closed — #{$ref}",
                "Booking #{$ref} for {$asset} is now fully closed. Thank you for using TheOnlineYard!",
                ['type' => 'booking_closed_client']
            );
        }
    }

    private function notifyCancelled($ns, $client, $owner, $asset, $ref, $booking): void
    {
        $reason = $booking->cancellation_reason ?? 'Not specified';

        if ($client) {
            $ns->sendToUser($client,
                "Booking Cancelled — #{$ref}",
                "Your booking #{$ref} for {$asset} has been cancelled. Reason: {$reason}. Any escrow funds will be refunded within 3-5 business days.",
                ['type' => 'booking_cancelled_client']
            );
        }

        if ($owner) {
            $ns->sendToUser($owner,
                "Booking Cancelled — #{$ref}",
                "Booking #{$ref} for {$asset} was cancelled. Reason: {$reason}. Your availability has been restored.",
                ['type' => 'booking_cancelled_owner']
            );
        }
    }
}
