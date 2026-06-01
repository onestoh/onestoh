<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\AiPricingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AiPricingController extends Controller
{
    public function __construct(private AiPricingService $service) {}

    public function getSuggestions(Request $request): JsonResponse
    {
        $owner = $request->user();
        $suggestions = $this->service->generateBulkSuggestions($owner);
        return response()->json(['data' => $suggestions]);
    }

    public function getSuggestion(Request $request, Asset $asset, string $durationType): JsonResponse
    {
        $this->authorize('update', $asset);
        $suggestion = $this->service->generateSuggestion($asset, $durationType);
        return response()->json(['data' => $suggestion]);
    }

    public function applyRate(Request $request, int $suggestionId): JsonResponse
    {
        $suggestion = \App\Models\AiPricingSuggestion::findOrFail($suggestionId);
        $this->authorize('update', $suggestion->asset);
        $validated = $request->validate(['custom_rate' => 'nullable|numeric|min:0']);
        $this->service->applyRecommendation($suggestion, $request->user(), $validated['custom_rate'] ?? null);
        return response()->json(['message' => 'Rate updated successfully']);
    }

    public function dismissSuggestion(Request $request, int $suggestionId): JsonResponse
    {
        $suggestion = \App\Models\AiPricingSuggestion::findOrFail($suggestionId);
        $this->authorize('update', $suggestion->asset);
        $suggestion->update(['owner_action' => 'dismissed']);
        return response()->json(['message' => 'Suggestion dismissed']);
    }

    public function peakSeasonForecast(Request $request): JsonResponse
    {
        $request->validate(['category' => 'required|string', 'county' => 'required|string']);
        $forecast = $this->service->getPeakSeasonForecast($request->category, $request->county);
        return response()->json(['data' => $forecast]);
    }
}
