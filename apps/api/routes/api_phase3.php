<?php
use App\Http\Controllers\Api\{
    AiPricingController, AiChatController, DemandForecastController,
    MultiCurrencyController, PublicApiController, MtnMomoController,
    StripeController, PhotoVerificationController
};
use App\Http\Controllers\Admin\{AdminFraudController, AdminTenantController};
use Illuminate\Support\Facades\Route;

// ── AI Features (authenticated) ────────────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // AI Pricing
    Route::get('/ai/pricing/suggestions',                           [AiPricingController::class, 'getSuggestions']);
    Route::get('/ai/pricing/suggestions/{asset}/{durationType}',    [AiPricingController::class, 'getSuggestion']);
    Route::post('/ai/pricing/suggestions/{suggestion}/apply',       [AiPricingController::class, 'applyRate']);
    Route::post('/ai/pricing/suggestions/{suggestion}/dismiss',     [AiPricingController::class, 'dismissSuggestion']);
    Route::get('/ai/pricing/peak-season',                          [AiPricingController::class, 'peakSeasonForecast']);

    // AI Chat
    Route::post('/ai/chat/session',                    [AiChatController::class, 'startSession']);
    Route::post('/ai/chat/message',                    [AiChatController::class, 'sendMessage']);
    Route::get('/ai/chat/{sessionToken}/history',      [AiChatController::class, 'getHistory']);
    Route::post('/ai/chat/{sessionToken}/escalate',    [AiChatController::class, 'escalate']);

    // Demand Forecasting
    Route::get('/ai/demand/alerts',           [DemandForecastController::class, 'ownerAlerts']);
    Route::get('/ai/demand/investment-signals',[DemandForecastController::class, 'investmentSignals']);
    Route::get('/ai/demand/forecast',         [DemandForecastController::class, 'getForecast']);

    // Multi-currency
    Route::get('/currencies',                 [MultiCurrencyController::class, 'supported']);
    Route::post('/currencies/convert',        [MultiCurrencyController::class, 'convert']);
    Route::post('/currencies/set-preferred',  [MultiCurrencyController::class, 'setPreferred']);

    // Public API client management
    Route::get('/api-clients',                               [PublicApiController::class, 'index']);
    Route::post('/api-clients',                              [PublicApiController::class, 'create']);
    Route::delete('/api-clients/{client}',                   [PublicApiController::class, 'delete']);
    Route::post('/api-clients/{client}/rotate-secret',       [PublicApiController::class, 'rotateSecret']);
    Route::get('/api-clients/{client}/webhook-deliveries',   [PublicApiController::class, 'webhookDeliveries']);
    Route::get('/api-clients/scopes',                        [PublicApiController::class, 'scopes']);

    // MTN MoMo (Uganda)
    Route::post('/payments/mtn-momo/initiate',           [MtnMomoController::class, 'initiate']);
    Route::get('/payments/mtn-momo/status/{requestId}',  [MtnMomoController::class, 'status']);

    // Stripe / international
    Route::post('/payments/stripe/intent',   [StripeController::class, 'createIntent']);
    Route::post('/payments/stripe/refund',   [StripeController::class, 'refund']);

    // Photo verification
    Route::post('/listings/{asset}/verify-photos',          [PhotoVerificationController::class, 'validateListing']);
    Route::post('/bookings/{booking}/compare-damage',       [PhotoVerificationController::class, 'compareDamage']);
});

// ── Unauthenticated AI chat (guest users) ──────────────────────────────────────────
Route::prefix('v1')->group(function () {
    Route::post('/ai/chat/guest-session', [AiChatController::class, 'startSession']);
    Route::post('/ai/chat/guest-message', [AiChatController::class, 'sendMessage']);
});

// ── Payment Webhooks (no auth) ───────────────────────────────────────────────────
Route::prefix('v1/webhooks')->group(function () {
    Route::post('/mtn-momo', [MtnMomoController::class, 'webhook']);
    Route::post('/stripe',   [StripeController::class, 'webhook']);
});

// ── Public API (API key auth) ─────────────────────────────────────────────────────
Route::middleware('public.api')->prefix('public/v1')->group(function () {
    Route::get('/listings', fn() => response()->json([
        'data' => \App\Models\Asset::with('media')
            ->where('is_published', true)
            ->paginate(20)
    ]));

    Route::get('/listings/{asset}', fn(\App\Models\Asset $asset) => response()->json([
        'data' => $asset->load('media', 'yard')
    ]));

    Route::get('/listings/{asset}/availability', function (
        \App\Models\Asset $asset,
        \App\Services\AvailabilityService $svc
    ) {
        return response()->json([
            'data' => $svc->getCalendar(
                $asset,
                now()->toDateString(),
                now()->addMonths(3)->toDateString()
            )
        ]);
    });

    Route::post('/bookings',          [\App\Http\Controllers\Api\BookingController::class, 'store']);
    Route::get('/bookings/{booking}', fn(\App\Models\Booking $booking) => response()->json(['data' => $booking]));
});

// ── Admin Phase 3 ─────────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {

    // Fraud management
    Route::get('/fraud/flags',                  [AdminFraudController::class, 'index']);
    Route::get('/fraud/flags/{flag}',           [AdminFraudController::class, 'show']);
    Route::post('/fraud/flags/{flag}/resolve',  [AdminFraudController::class, 'resolve']);
    Route::get('/fraud/risk-profiles',          [AdminFraudController::class, 'riskProfiles']);
    Route::post('/fraud/bulk-suspend',          [AdminFraudController::class, 'bulkSuspend']);

    // Tenant / white-label management
    Route::get('/tenants',                              [AdminTenantController::class, 'index']);
    Route::post('/tenants',                             [AdminTenantController::class, 'create']);
    Route::get('/tenants/stats',                        [AdminTenantController::class, 'stats']);
    Route::get('/tenants/{tenant}',                     [AdminTenantController::class, 'show']);
    Route::put('/tenants/{tenant}',                     [AdminTenantController::class, 'update']);
    Route::post('/tenants/{tenant}/provision-gateway',  [AdminTenantController::class, 'provisionGateway']);
});
