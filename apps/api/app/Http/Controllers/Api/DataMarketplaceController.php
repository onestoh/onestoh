<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{DataProduct, DataProductSubscription};
use App\Services\DataMarketplaceService;
use Illuminate\Http\{JsonResponse, Request};

class DataMarketplaceController extends Controller
{
    public function __construct(private DataMarketplaceService $service) {}

    /**
     * Public: list all active data products.
     */
    public function getProducts(): JsonResponse
    {
        return response()->json(['data' => $this->service->getAvailableProducts()]);
    }

    /**
     * Subscribe to a data product.
     */
    public function subscribe(Request $request, DataProduct $product): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email',
            'type'          => 'sometimes|in:company,individual',
            'billing_cycle' => 'required|in:monthly,annual',
        ]);

        $subscription = $this->service->createSubscription($product, $data, $data['billing_cycle']);
        return response()->json([
            'data'    => $subscription,
            'api_key' => $subscription->api_key, // only revealed once
            'message' => 'Subscription created. Store your API key securely — it will not be shown again.',
        ], 201);
    }

    /**
     * API-key authenticated: get market report.
     */
    public function getMarketReport(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string',
            'period'   => 'sometimes|in:7d,30d,quarter',
        ]);

        $sub = $request->attributes->get('data_subscription');
        if ($sub) {
            $this->service->trackApiUsage($sub, 'market-report');
        }

        $report = $this->service->generateMarketReport(
            $request->category,
            $request->get('period', '30d')
        );
        return response()->json(['data' => $report]);
    }

    /**
     * API-key authenticated: get pricing index.
     */
    public function getPricingIndex(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string',
            'county'   => 'required|string',
        ]);

        $sub = $request->attributes->get('data_subscription');
        if ($sub) {
            $this->service->trackApiUsage($sub, 'pricing-index');
        }

        $index = $this->service->getPricingIndex($request->category, $request->county);
        return response()->json(['data' => $index]);
    }

    /**
     * API-key authenticated: get fleet valuation estimate.
     */
    public function getFleetValuation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category' => 'required|string',
            'year'     => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'make'     => 'sometimes|string',
        ]);

        $sub = $request->attributes->get('data_subscription');
        if ($sub) {
            $this->service->trackApiUsage($sub, 'fleet-valuation');
        }

        $valuation = $this->service->getFleetValuationEstimate($data);
        return response()->json(['data' => $valuation]);
    }
}
