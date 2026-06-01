<?php
/**
 * Phase 2 API routes for TheOnlineYard.
 * Include this file from routes/api.php:
 *   require __DIR__ . '/api_phase2.php';
 */

use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\CorporateController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\FleetController;
use App\Http\Controllers\Api\FlutterwaveController;
use App\Http\Controllers\Api\HeavyEquipmentController;
use App\Http\Controllers\Api\InsuranceController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\SalesController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// All Phase 2 routes require authentication unless explicitly excluded
// ---------------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // -----------------------------------------------------------------------
    // Fleet ERP
    // -----------------------------------------------------------------------
    Route::prefix('fleet')->group(function () {
        Route::get('/summary',              [FleetController::class, 'summary']);
        Route::get('/calendar',             [FleetController::class, 'calendar']);
        Route::get('/analytics',            [FleetController::class, 'analytics']);
        Route::post('/maintenance',         [FleetController::class, 'logMaintenance']);
        Route::get('/maintenance',          [FleetController::class, 'maintenanceHistory']);
        Route::post('/maintenance/schedule',[FleetController::class, 'createSchedule']);
        Route::get('/idle-assets',          [FleetController::class, 'idleAssets']);
    });

    // -----------------------------------------------------------------------
    // Heavy Equipment
    // -----------------------------------------------------------------------
    Route::post('/heavy-equipment/{asset}/project-booking', [HeavyEquipmentController::class, 'createProjectBooking']);
    Route::post('/bookings/{booking}/owner-approve',         [HeavyEquipmentController::class, 'ownerApprove']);
    Route::post('/bookings/{booking}/owner-decline',         [HeavyEquipmentController::class, 'ownerDecline']);

    // -----------------------------------------------------------------------
    // Sales Module
    // -----------------------------------------------------------------------
    Route::prefix('sales')->group(function () {
        Route::post('/enquiry',                   [SalesController::class, 'createEnquiry']);
        Route::post('/offers/{offer}/counter',    [SalesController::class, 'counterOffer']);
        Route::post('/offers/{offer}/agree',      [SalesController::class, 'agreePrice']);
        Route::post('/offers/{offer}/reserve',    [SalesController::class, 'payReservation']);
        Route::get('/offers',                     [SalesController::class, 'myOffers']);
        Route::post('/test-drive',                [SalesController::class, 'scheduleTestDrive']);
        Route::get('/test-drives',                [SalesController::class, 'myTestDrives']);
    });

    // -----------------------------------------------------------------------
    // Corporate Accounts
    // -----------------------------------------------------------------------
    Route::prefix('corporate')->group(function () {
        Route::post('/',                             [CorporateController::class, 'create']);
        Route::get('/',                              [CorporateController::class, 'show']);
        Route::post('/members',                      [CorporateController::class, 'inviteMember']);
        Route::delete('/members/{member}',           [CorporateController::class, 'removeMember']);
        Route::post('/bookings/{booking}/approve',   [CorporateController::class, 'approveBooking']);
        Route::get('/invoice/{month}',               [CorporateController::class, 'monthlyInvoice']);
    });

    // -----------------------------------------------------------------------
    // Insurance
    // -----------------------------------------------------------------------
    Route::get('/bookings/{booking}/insurance-products', [InsuranceController::class, 'products']);
    Route::post('/bookings/{booking}/insurance',         [InsuranceController::class, 'addInsurance']);
    Route::post('/insurance/{policy}/claim',             [InsuranceController::class, 'fileClaim']);
    Route::get('/insurance/{policy}/certificate',        [InsuranceController::class, 'certificate']);

    // -----------------------------------------------------------------------
    // Analytics
    // -----------------------------------------------------------------------
    Route::get('/analytics/owner',  [AnalyticsController::class, 'ownerAnalytics']);
    Route::get('/analytics/broker', [AnalyticsController::class, 'brokerAnalytics']);
    Route::get('/analytics/client', [AnalyticsController::class, 'clientAnalytics']);

    // -----------------------------------------------------------------------
    // Driver Pool
    // -----------------------------------------------------------------------
    Route::get('/drivers/available',              [DriverController::class, 'available']);
    Route::post('/drivers/availability',          [DriverController::class, 'setAvailability']);
    Route::get('/drivers/{driver}/performance',   [DriverController::class, 'performance']);

    // -----------------------------------------------------------------------
    // Listing Promotions
    // -----------------------------------------------------------------------
    Route::get('/promotions/pricing',               [PromotionController::class, 'pricing']);
    Route::post('/promotions',                      [PromotionController::class, 'create']);
    Route::get('/promotions',                       [PromotionController::class, 'myPromotions']);
    Route::post('/promotions/{promotion}/activate', [PromotionController::class, 'activate']);

    // -----------------------------------------------------------------------
    // Card Payments (Flutterwave)
    // -----------------------------------------------------------------------
    Route::post('/payments/flutterwave/initiate',       [FlutterwaveController::class, 'initiate']);
    Route::get('/payments/flutterwave/verify/{txRef}',  [FlutterwaveController::class, 'verify']);

    // -----------------------------------------------------------------------
    // Admin Analytics (admin middleware applied inside the group)
    // -----------------------------------------------------------------------
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/analytics/platform',      [AdminAnalyticsController::class, 'platformSnapshot']);
        Route::get('/analytics/funnel',        [AdminAnalyticsController::class, 'conversionFunnel']);
        Route::get('/analytics/geographic',    [AdminAnalyticsController::class, 'geographicBreakdown']);
        Route::get('/analytics/top-performers',[AdminAnalyticsController::class, 'topPerformers']);
        Route::get('/analytics/growth',        [AdminAnalyticsController::class, 'growthMetrics']);
    });

}); // end auth:sanctum

// ---------------------------------------------------------------------------
// Webhook — must be OUTSIDE auth middleware
// ---------------------------------------------------------------------------
Route::post('/payments/flutterwave/webhook', [FlutterwaveController::class, 'webhook']);
