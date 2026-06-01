<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\ListingPromotion;
use Illuminate\Support\Facades\Redis;

class PromotionService
{
    // KES per week per promotion type
    private const PRICING = [
        'featured_listing'   => 1000,
        'category_placement' => 500,
        'homepage_banner'    => 3000,
    ];

    /**
     * Return tier pricing table.
     */
    public function getPromotionPricing(): array
    {
        return collect(self::PRICING)->map(fn ($price, $type) => [
            'type'           => $type,
            'price_per_week' => $price,
        ])->values()->toArray();
    }

    /**
     * Calculate cost and create a pending promotion record.
     */
    public function createPromotion(Asset $asset, string $type, int $days): ListingPromotion
    {
        $weeks  = max(1, (int) ceil($days / 7));
        $amount = ($this::PRICING[$type] ?? 1000) * $weeks;

        return ListingPromotion::create([
            'asset_id'   => $asset->id,
            'owner_id'   => $asset->owner_id,
            'type'       => $type,
            'amount_paid'=> $amount,
            'starts_at'  => now()->toDateString(),
            'ends_at'    => now()->addDays($days)->toDateString(),
            'status'     => 'pending_payment',
        ]);
    }

    /**
     * Activate a promotion after payment confirmation.
     */
    public function activatePromotion(ListingPromotion $promo, string $paymentRef): ListingPromotion
    {
        $promo->update([
            'status'            => 'active',
            'payment_reference' => $paymentRef,
        ]);

        // Bust listing cache
        Redis::del('listing:featured');
        Redis::del('listing:category:' . optional($promo->asset)->category);

        return $promo->fresh();
    }

    /**
     * Track a promotion impression — increments Redis counter, batches to DB every 100.
     */
    public function trackImpression(int $assetId): void
    {
        $key   = "promo:impressions:{$assetId}";
        $count = Redis::incr($key);

        if ($count % 100 === 0) {
            ListingPromotion::where('asset_id', $assetId)
                ->where('status', 'active')
                ->increment('impressions', 100);
            Redis::set($key, 0);
        }
    }

    /**
     * Track a promotion click.
     */
    public function trackClick(int $assetId): void
    {
        $key   = "promo:clicks:{$assetId}";
        $count = Redis::incr($key);

        if ($count % 50 === 0) {
            ListingPromotion::where('asset_id', $assetId)
                ->where('status', 'active')
                ->increment('clicks', 50);
            Redis::set($key, 0);
        }
    }

    /**
     * Mark expired promotions and remove featured badges from listing cache.
     */
    public function expireOldPromotions(): void
    {
        $expired = ListingPromotion::where('status', 'active')
            ->where('ends_at', '<', now()->toDateString())
            ->get();

        foreach ($expired as $promo) {
            $promo->update(['status' => 'expired']);
            Redis::del('listing:featured');
            Redis::del('listing:category:' . optional($promo->asset)->category);
        }
    }
}
