<?php
use App\Http\Controllers\Admin\AdminDisputeController;
use App\Http\Controllers\Admin\AdminKycController;
use App\Http\Controllers\Admin\AdminListingController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BrokerController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — TheOnlineYard
|--------------------------------------------------------------------------
*/

// ─── Public routes ───────────────────────────────────────────────────────────

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Public listings
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);
    Route::get('/listings/{assetId}/availability', [AvailabilityController::class, 'calendar']);
    Route::get('/listings/{assetId}/check-availability', [AvailabilityController::class, 'checkAvailability']);
    Route::get('/reviews', [ReviewController::class, 'index']);

    // Referral tracking (public)
    Route::post('/referral/track', [BrokerController::class, 'trackClick']);

    // MPesa webhooks (no auth — Safaricom callbacks)
    Route::post('/webhooks/mpesa/stk-callback', [PaymentController::class, 'mpesaStkCallback'])
        ->name('mpesa.stk.callback');
    Route::post('/webhooks/mpesa/b2c-result', [PaymentController::class, 'mpesaB2cResult'])
        ->name('mpesa.b2c.result');
    Route::post('/webhooks/mpesa/b2c-timeout', [PaymentController::class, 'mpesaB2cTimeout'])
        ->name('mpesa.b2c.timeout');

    // ─── Authenticated routes ─────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
        Route::post('/auth/send-phone-otp', [AuthController::class, 'sendPhoneOtp']);
        Route::post('/auth/verify-phone', [AuthController::class, 'verifyPhoneOtp']);
        Route::post('/auth/verify-email', [AuthController::class, 'verifyEmailOtp']);

        // KYC
        Route::get('/kyc/status', [KycController::class, 'status']);
        Route::post('/kyc/upload', [KycController::class, 'upload']);
        Route::get('/kyc/documents/{id}/url', [KycController::class, 'getDocumentUrl']);

        // Listings management (owner)
        Route::get('/my-listings', [ListingController::class, 'myListings']);
        Route::post('/listings', [ListingController::class, 'store']);
        Route::put('/listings/{id}', [ListingController::class, 'update']);
        Route::delete('/listings/{id}', [ListingController::class, 'destroy']);
        Route::post('/listings/{id}/media', [ListingController::class, 'uploadMedia']);
        Route::delete('/listings/{assetId}/media/{mediaId}', [ListingController::class, 'deleteMedia']);

        // Availability management (owner)
        Route::post('/listings/{assetId}/block-dates', [AvailabilityController::class, 'blockDates']);
        Route::delete('/listings/{assetId}/block-dates', [AvailabilityController::class, 'unblockDates']);

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
        Route::patch('/bookings/{id}/status', [BookingController::class, 'advanceStatus']);
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
        Route::post('/bookings/{id}/dispute', [BookingController::class, 'raiseDispute']);
        Route::post('/bookings/{id}/pre-rental-photos', [BookingController::class, 'submitPreRentalPhotos']);
        Route::post('/bookings/{id}/post-rental-photos', [BookingController::class, 'submitPostRentalPhotos']);

        // Owner booking view
        Route::get('/owner/bookings', [BookingController::class, 'ownerBookings']);

        // Payments
        Route::post('/payments/mpesa-stk', [PaymentController::class, 'initiateMpesaStk']);
        Route::get('/payments/{id}/status', [PaymentController::class, 'queryPaymentStatus']);
        Route::get('/bookings/{bookingId}/payments', [PaymentController::class, 'bookingPayments']);

        // Wallet
        Route::get('/wallet/balance', [WalletController::class, 'balance']);
        Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
        Route::post('/wallet/payout', [WalletController::class, 'requestPayout']);

        // Reviews
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::post('/reviews/{id}/owner-response', [ReviewController::class, 'ownerResponse']);

        // Broker
        Route::get('/broker/stats', [BrokerController::class, 'stats']);
        Route::get('/broker/commissions', [BrokerController::class, 'commissionHistory']);
        Route::post('/broker/link', [BrokerController::class, 'generateLink']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

        // ─── Admin routes ─────────────────────────────────────────────────────
        Route::middleware('admin')->prefix('admin')->group(function () {

            // KYC
            Route::get('/kyc/queue', [AdminKycController::class, 'queue']);
            Route::get('/kyc/users/{userId}/documents', [AdminKycController::class, 'userDocuments']);
            Route::post('/kyc/documents/{docId}/approve', [AdminKycController::class, 'approveDocument']);
            Route::post('/kyc/documents/{docId}/reject', [AdminKycController::class, 'rejectDocument']);
            Route::post('/kyc/users/{userId}/approve', [AdminKycController::class, 'approveUserKyc']);
            Route::post('/kyc/users/{userId}/reject', [AdminKycController::class, 'rejectUserKyc']);

            // Users
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::get('/users/{id}', [AdminUserController::class, 'show']);
            Route::post('/users/{id}/suspend', [AdminUserController::class, 'suspend']);
            Route::post('/users/{id}/restore', [AdminUserController::class, 'restore']);
            Route::patch('/users/{id}/role', [AdminUserController::class, 'updateRole']);
            Route::patch('/users/{id}/trust-score', [AdminUserController::class, 'adjustTrustScore']);

            // Listings
            Route::get('/listings', [AdminListingController::class, 'index']);
            Route::get('/listings/pending', [AdminListingController::class, 'pending']);
            Route::post('/listings/{id}/approve', [AdminListingController::class, 'approve']);
            Route::post('/listings/{id}/reject', [AdminListingController::class, 'reject']);
            Route::post('/listings/{id}/unpublish', [AdminListingController::class, 'unpublish']);

            // Payments & Escrow
            Route::get('/payments', [AdminPaymentController::class, 'payments']);
            Route::get('/payments/stats', [AdminPaymentController::class, 'paymentStats']);
            Route::get('/escrow', [AdminPaymentController::class, 'escrowAccounts']);
            Route::post('/escrow/{bookingId}/release', [AdminPaymentController::class, 'releaseEscrow']);
            Route::post('/escrow/{bookingId}/split', [AdminPaymentController::class, 'escrowSplit']);

            // Disputes
            Route::get('/disputes', [AdminDisputeController::class, 'index']);
            Route::get('/disputes/{id}', [AdminDisputeController::class, 'show']);
            Route::post('/disputes/{id}/assign', [AdminDisputeController::class, 'assign']);
            Route::post('/disputes/{id}/rule', [AdminDisputeController::class, 'rule']);
            Route::post('/disputes/{id}/appeal', [AdminDisputeController::class, 'processAppeal']);
        });
    });
});
