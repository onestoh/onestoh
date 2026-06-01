<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\ListingPromotion;
use App\Services\PromotionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function __construct(private PromotionService $promotionService) {}

    /** GET /promotions/pricing */
    public function pricing(): JsonResponse
    {
        return response()->json($this->promotionService->getPromotionPricing());
    }

    /** POST /promotions */
    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'type'     => 'required|in:featured_listing,category_placement,homepage_banner',
            'days'     => 'required|integer|min:7|max:90',
        ]);

        $asset = Asset::where('id', $data['asset_id'])
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $promo = $this->promotionService->createPromotion($asset, $data['type'], $data['days']);
        return response()->json($promo, 201);
    }

    /** GET /promotions */
    public function myPromotions(): JsonResponse
    {
        $promos = ListingPromotion::where('owner_id', Auth::id())
            ->with('asset:id,name')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($promos);
    }

    /** POST /promotions/{promotion}/activate */
    public function activate(Request $request, ListingPromotion $promotion): JsonResponse
    {
        $data = $request->validate(['payment_reference' => 'required|string']);

        abort_if($promotion->owner_id !== Auth::id(), 403);

        $promo = $this->promotionService->activatePromotion($promotion, $data['payment_reference']);
        return response()->json($promo);
    }
}
