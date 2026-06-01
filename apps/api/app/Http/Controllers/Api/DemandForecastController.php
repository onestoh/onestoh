<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DemandForecastService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DemandForecastController extends Controller
{
    public function __construct(private DemandForecastService $service) {}

    public function ownerAlerts(Request $request): JsonResponse
    {
        $spikes = $this->service->getUpcomingDemandSpikes($request->user());
        return response()->json(['data' => $spikes]);
    }

    public function investmentSignals(Request $request): JsonResponse
    {
        $signals = $this->service->getFleetInvestmentSignal($request->user());
        return response()->json(['data' => $signals]);
    }

    public function getForecast(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string',
            'county'   => 'required|string',
            'period'   => 'in:day,week,month',
        ]);
        $forecast = $this->service->getForecast($request->category, $request->county, $request->period ?? 'month');
        return response()->json(['data' => $forecast]);
    }
}
