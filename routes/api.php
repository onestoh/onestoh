<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PropertyApiController;
use App\Http\Controllers\Api\V1\AuctionApiController;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\UserApiController;
use App\Http\Controllers\SearchController;

// Public endpoints
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/auth/login', [AuthApiController::class, 'login']);
    Route::post('/auth/register', [AuthApiController::class, 'register']);

    // Properties (public browse)
    Route::get('/properties', [PropertyApiController::class, 'index']);
    Route::get('/properties/{id}', [PropertyApiController::class, 'show']);

    // Auctions (public browse)
    Route::get('/auctions', [AuctionApiController::class, 'index']);
    Route::get('/auctions/{id}', [AuctionApiController::class, 'show']);

    // Search suggestions
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);

    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {

        // User profile
        Route::get('/user', [AuthApiController::class, 'me']);

        // User resources
        Route::get('/user/properties', [UserApiController::class, 'properties']);
        Route::get('/user/leases', [UserApiController::class, 'leases']);
        Route::get('/user/payments', [UserApiController::class, 'payments']);
        Route::get('/user/messages', [UserApiController::class, 'messages']);
        Route::post('/messages', [UserApiController::class, 'sendMessage']);
        Route::get('/user/notifications', [UserApiController::class, 'notifications']);

        // Create property
        Route::post('/properties', [PropertyApiController::class, 'store']);

        // Place bid
        Route::post('/auctions/{id}/bid', [AuctionApiController::class, 'bid']);

        // Rent payment
        Route::post('/rent/pay', [\App\Http\Controllers\RentPaymentController::class, 'initiate']);
    });
});
