<?php
use App\Http\Controllers\Api\{
    TelematicsController, FinancingController, ProcurementController,
    ErpController, DataMarketplaceController, YardGroupController
};
use App\Http\Controllers\Admin\AdminProcurementController;
use Illuminate\Support\Facades\Route;

// ── IoT Telematics (device API key, not user Sanctum) ─────────────────────────────────
Route::prefix('v1/telematics')->group(function () {
    Route::post('/ping', [TelematicsController::class, 'ingestPing'])->middleware('telematics.device');
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Telematics (owner/admin)
    Route::get('/assets/{asset}/location', [TelematicsController::class, 'getLiveLocation']);
    Route::get('/assets/{asset}/route', [TelematicsController::class, 'getAssetRoute']);
    Route::get('/assets/{asset}/trips', [TelematicsController::class, 'getTripHistory']);
    Route::get('/assets/{asset}/geofences', [TelematicsController::class, 'getGeofences']);
    Route::post('/assets/{asset}/geofences', [TelematicsController::class, 'createGeofence']);
    Route::delete('/geofences/{geofence}', [TelematicsController::class, 'deleteGeofence']);
    Route::get('/driver-behaviour', [TelematicsController::class, 'getDriverBehaviourReport']);

    // Fleet Financing
    Route::get('/financing/partners/{asset}', [FinancingController::class, 'getEligiblePartners']);
    Route::post('/financing/calculate', [FinancingController::class, 'calculateRepayment']);
    Route::post('/financing/apply', [FinancingController::class, 'submitApplication']);
    Route::get('/financing/applications', [FinancingController::class, 'getMyApplications']);
    Route::get('/financing/applications/{app}', [FinancingController::class, 'getApplicationStatus']);
    Route::get('/financing/leases', [FinancingController::class, 'getLeaseAgreements']);
    Route::get('/financing/leases/{agreement}/schedule', [FinancingController::class, 'getLeaseSchedule']);

    // Government Procurement
    Route::get('/procurement/tenders', [ProcurementController::class, 'getOpenTenders']);
    Route::get('/procurement/tenders/{tender}', [ProcurementController::class, 'getTender']);
    Route::post('/procurement/tenders/{tender}/bid', [ProcurementController::class, 'submitBid']);
    Route::get('/procurement/my-bids', [ProcurementController::class, 'getMyBids']);
    Route::get('/procurement/compliance', [ProcurementController::class, 'getComplianceReport']);

    // ERP Integrations
    Route::get('/erp/platforms', [ErpController::class, 'getSupportedPlatforms']);
    Route::post('/erp/connect/{platform}', [ErpController::class, 'connect']);
    Route::get('/erp/status', [ErpController::class, 'getStatus']);
    Route::post('/erp/{integration}/sync', [ErpController::class, 'triggerSync']);
    Route::get('/erp/{integration}/logs', [ErpController::class, 'getSyncLogs']);
    Route::delete('/erp/{integration}', [ErpController::class, 'disconnect']);

    // YardGroup SSO
    Route::post('/yardgroup/sso/generate', [YardGroupController::class, 'generateSsoToken']);
    Route::get('/yardgroup/profile', [YardGroupController::class, 'getUnifiedProfile']);
    Route::get('/yardgroup/cross-referrals', [YardGroupController::class, 'getCrossReferralStats']);
});

// ── YardGroup SSO validate (called by other platforms, shared secret auth) ────────────
Route::post('/v1/yardgroup/sso/validate', [YardGroupController::class, 'validateSsoToken'])
    ->middleware('yardgroup.shared_secret');

// ── Data Marketplace (public + API key) ────────────────────────────────────────────
Route::get('/v1/data/products', [DataMarketplaceController::class, 'getProducts']);
Route::middleware('auth:sanctum')->post('/v1/data/subscribe', [DataMarketplaceController::class, 'subscribe']);
Route::middleware('data.api_key')->prefix('v1/data')->group(function () {
    Route::get('/market-report', [DataMarketplaceController::class, 'getMarketReport']);
    Route::get('/pricing-index', [DataMarketplaceController::class, 'getPricingIndex']);
    Route::get('/fleet-valuation', [DataMarketplaceController::class, 'getFleetValuation']);
});

// ── Paystack Webhooks (no auth) ────────────────────────────────────────────────────
Route::post('/v1/webhooks/paystack', [\App\Http\Controllers\Api\PaystackController::class, 'webhook']);

// ── Admin Procurement ────────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {
    Route::get('/procurement/tenders', [AdminProcurementController::class, 'getTenders']);
    Route::post('/procurement/tenders', [AdminProcurementController::class, 'createTender']);
    Route::post('/procurement/tenders/{tender}/evaluate', [AdminProcurementController::class, 'evaluateBids']);
    Route::post('/procurement/tenders/{tender}/award', [AdminProcurementController::class, 'awardTender']);
    Route::get('/procurement/entities', [AdminProcurementController::class, 'getGovernmentEntities']);
});
