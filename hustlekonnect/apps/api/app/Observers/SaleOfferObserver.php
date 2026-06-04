<?php
namespace App\Observers;

use App\Models\SaleOffer;
use App\Models\PlatformNotification;
use Illuminate\Support\Str;

class SaleOfferObserver
{
    public function updated(SaleOffer $offer): void
    {
        if (!$offer->isDirty('status')) return;

        $asset  = $offer->asset?->title ?? 'Vehicle';
        $ref    = strtoupper(substr($offer->id, 0, 8));
        $amount = number_format($offer->offered_price ?? $offer->asking_price ?? 0);

        $messages = match ($offer->status) {
            'offer_made' => [
                [$offer->seller_id, '💰 New Offer Received', "You received a KES {$amount} offer on {$asset}. Reference #{$ref}."],
                [$offer->buyer_id,  '✅ Offer Submitted', "Your KES {$amount} offer on {$asset} is pending seller review."],
            ],
            'offer_accepted' => [
                [$offer->buyer_id,  '🎉 Offer Accepted!', "Your offer on {$asset} was accepted! Proceed to payment. Ref #{$ref}."],
                [$offer->seller_id, '✔️ You Accepted an Offer', "You accepted a KES {$amount} offer on {$asset}. Awaiting payment."],
            ],
            'sale_completed' => [
                [$offer->buyer_id,  '🚗 Sale Complete!', "Congratulations! {$asset} is now yours. Ref #{$ref}."],
                [$offer->seller_id, '💵 Sale Complete — Payment Released', "KES {$amount} from the sale of {$asset} has been released to your wallet."],
            ],
            default => [],
        };

        foreach ($messages as [$userId, $title, $body]) {
            PlatformNotification::create([
                'id'      => (string) Str::uuid(),
                'user_id' => $userId,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'sale_' . $offer->status,
                'data'    => ['sale_offer_id' => $offer->id],
            ]);
        }
    }
}
