<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    /** GET /analytics/owner?period=30d */
    public function ownerAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', '30d');
        $this->validatePeriod($period);
        return response()->json($this->analyticsService->getOwnerAnalytics(Auth::user(), $period));
    }

    /** GET /analytics/broker?period=30d */
    public function brokerAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', '30d');
        $this->validatePeriod($period);
        return response()->json($this->analyticsService->getBrokerAnalytics(Auth::user(), $period));
    }

    /** GET /analytics/client */
    public function clientAnalytics(): JsonResponse
    {
        return response()->json($this->analyticsService->getClientAnalytics(Auth::user()));
    }

    private function validatePeriod(string $period): void
    {
        abort_if(!in_array($period, ['7d', '30d', '90d', '1y']), 422, 'Invalid period. Use 7d, 30d, 90d or 1y.');
    }
}
