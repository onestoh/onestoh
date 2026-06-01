<?php
namespace App\Observers;

use App\Models\SaleOffer;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class SaleOfferObserver
{
    public function __construct(private NotificationService $notifications) {}

    public function updated(SaleOffer $offer): void
    {
        if (!$offer->isDirty('status')) {
            return;
        }

        $oldStatus = $offer->getOriginal('status');
        $newStatus = $offer->status;

        Log::info("SaleOffer status transition", ['offer_id' => $offer->id, 'from' => $oldStatus, 'to' => $newStatus]);

        $offer->loadMissing(['buyer', 'asset.yard.user']);

        match ($newStatus) {
            'accepted' => $this->onAccepted($offer),
            'rejected' => $this->onRejected($offer),
            'payment_pending' => $this->onPaymentPending($offer),
            'payment_completed' => $this->onPaymentCompleted($offer),
            'ownership_transferred' => $this->onOwnershipTransferred($offer),
            default => null,
        };
    }

    private function onAccepted(SaleOffer $offer): void
    {
        $seller = $offer->asset?->yard?->user;
        $buyer = $offer->buyer;

        if ($buyer) {
            $this->notifications->sendToUser($buyer, 'Offer Accepted!',
                "Your offer of KES {$offer->offer_price_kes} for {$offer->asset?->title} has been accepted. Proceed to payment.",
                ['type' => 'sale_offer_accepted', 'offer_id' => $offer->id]
            );
        }

        if ($seller) {
            $this->notifications->sendToUser($seller, 'Sale Offer Accepted',
                "You accepted a sale offer of KES {$offer->offer_price_kes} for {$offer->asset?->title}. Awaiting buyer payment.",
                ['type' => 'sale_offer_seller_accepted', 'offer_id' => $offer->id]
            );
        }
    }

    private function onRejected(SaleOffer $offer): void
    {
        $buyer = $offer->buyer;
        if ($buyer) {
            $this->notifications->sendToUser($buyer, 'Offer Not Accepted',
                "Your offer for {$offer->asset?->title} was not accepted. You can make a revised offer.",
                ['type' => 'sale_offer_rejected', 'offer_id' => $offer->id]
            );
        }
    }

    private function onPaymentPending(SaleOffer $offer): void
    {
        $seller = $offer->asset?->yard?->user;
        if ($seller) {
            $this->notifications->sendToUser($seller, 'Buyer Payment In Progress',
                "The buyer is processing payment for {$offer->asset?->title}.",
                ['type' => 'sale_payment_pending', 'offer_id' => $offer->id]
            );
        }
    }

    private function onPaymentCompleted(SaleOffer $offer): void
    {
        $seller = $offer->asset?->yard?->user;
        $buyer = $offer->buyer;

        if ($buyer) {
            $this->notifications->sendToUser($buyer, 'Payment Confirmed — Sale Proceeding',
                "Your payment for {$offer->asset?->title} has been confirmed. The seller will arrange document transfer.",
                ['type' => 'sale_payment_confirmed_buyer', 'offer_id' => $offer->id]
            );
        }

        if ($seller) {
            $this->notifications->sendToUser($seller, 'Payment Received — Transfer Documents',
                "Payment of KES {$offer->offer_price_kes} for {$offer->asset?->title} is in escrow. Please arrange ownership documents.",
                ['type' => 'sale_payment_confirmed_seller', 'offer_id' => $offer->id]
            );
        }
    }

    private function onOwnershipTransferred(SaleOffer $offer): void
    {
        $buyer = $offer->buyer;
        $seller = $offer->asset?->yard?->user;

        if ($buyer) {
            $this->notifications->sendToUser($buyer, 'Vehicle Ownership Transferred!',
                "Congratulations! {$offer->asset?->title} is now yours. Documents have been transferred.",
                ['type' => 'sale_ownership_transferred_buyer', 'offer_id' => $offer->id]
            );
        }

        if ($seller) {
            $this->notifications->sendToUser($seller, 'Sale Complete — Funds Released',
                "Ownership of {$offer->asset?->title} has been transferred. Funds of KES {$offer->offer_price_kes} will be released to your wallet within 24 hours.",
                ['type' => 'sale_ownership_transferred_seller', 'offer_id' => $offer->id]
            );
        }
    }
}
