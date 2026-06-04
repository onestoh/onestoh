<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\BrokerController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\Admin\AdminKycController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\AdminListingController;
use App\Http\Controllers\Api\Admin\AdminPaymentController;
use App\Http\Controllers\Api\Admin\AdminDisputeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Auth ──────────────────────────────────────────────────────
    Route::prefix('auth')->middleware(['sql.protect', 'throttle.login'])->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('resend-otp', [AuthController::class, 'resendOtp']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // ── Public Listings ───────────────────────────────────────────
    Route::middleware(['sql.protect', 'throttle:api'])->group(function () {
        Route::get('listings',        [ListingController::class, 'index']);
        Route::get('listings/{id}',   [ListingController::class, 'show']);
        Route::get('listings/{id}/availability', [AvailabilityController::class, 'show']);
        Route::get('markets',         [\App\Http\Controllers\Api\MultiCurrencyController::class, 'markets']);
        Route::get('currencies',      [\App\Http\Controllers\Api\MultiCurrencyController::class, 'rates']);
    });

    // ── Payment Webhooks (no auth, signature-verified) ────────────
    Route::post('payments/mpesa/callback',    [PaymentController::class, 'mpesaCallback']);
    Route::post('payments/mpesa/b2c-result',  [PaymentController::class, 'mpesaB2cResult']);
    Route::post('payments/flutterwave/webhook', [\App\Http\Controllers\Api\FlutterwaveController::class, 'webhook']);
    Route::post('payments/paystack/webhook',  [\App\Http\Controllers\Api\PaystackController::class, 'webhook']);
    Route::post('payments/stripe/webhook',    [\App\Http\Controllers\Api\StripeController::class, 'webhook']);
    Route::post('payments/mtn-momo/callback', [\App\Http\Controllers\Api\MtnMomoController::class, 'callback']);

    // ── Authenticated Routes ──────────────────────────────────────
    Route::middleware(['auth:sanctum', 'sql.protect', 'throttle:api'])->group(function () {

        Route::post('auth/logout',  [AuthController::class, 'logout']);
        Route::get('auth/me',       [AuthController::class, 'me']);
        Route::put('auth/profile',  [AuthController::class, 'updateProfile']);
        Route::put('auth/password', [AuthController::class, 'changePassword']);

        // KYC
        Route::prefix('kyc')->group(function () {
            Route::get('status',   [KycController::class, 'status']);
            Route::post('submit',  [KycController::class, 'submit']);
        });

        // Listings (owner)
        Route::post('listings',           [ListingController::class, 'store'])->middleware('kyc.verified');
        Route::put('listings/{id}',       [ListingController::class, 'update']);
        Route::delete('listings/{id}',    [ListingController::class, 'destroy']);
        Route::post('listings/{id}/media',[ListingController::class, 'uploadMedia']);

        // Availability
        Route::post('availability/hold',      [AvailabilityController::class, 'hold']);
        Route::delete('availability/hold/{id}', [AvailabilityController::class, 'releaseHold']);
        Route::put('availability/{assetId}',  [AvailabilityController::class, 'update']);

        // Bookings
        Route::middleware('kyc.verified')->group(function () {
            Route::post('bookings',              [BookingController::class, 'store']);
            Route::get('bookings',               [BookingController::class, 'index']);
            Route::get('bookings/{id}',          [BookingController::class, 'show']);
            Route::post('bookings/{id}/cancel',  [BookingController::class, 'cancel']);
            Route::post('bookings/{id}/confirm-return', [BookingController::class, 'confirmReturn']);
            Route::post('bookings/{id}/dispute', [BookingController::class, 'dispute']);
        });

        // Payments
        Route::middleware(['kyc.verified', 'throttle:payments'])->group(function () {
            Route::post('payments/initiate',       [PaymentController::class, 'initiate']);
            Route::get('payments/{id}/status',     [PaymentController::class, 'status']);
            Route::post('payments/wallet/pay',     [PaymentController::class, 'payWithWallet']);
        });

        // Wallet
        Route::prefix('wallet')->group(function () {
            Route::get('balance',        [WalletController::class, 'balance']);
            Route::get('transactions',   [WalletController::class, 'transactions']);
            Route::post('withdraw',      [WalletController::class, 'withdraw']);
            Route::post('topup',         [WalletController::class, 'topup']);
        });

        // Reviews
        Route::post('reviews',         [ReviewController::class, 'store']);
        Route::get('reviews/asset/{assetId}', [ReviewController::class, 'forAsset']);
        Route::get('reviews/user/{userId}',   [ReviewController::class, 'forUser']);

        // Notifications
        Route::get('notifications',           [NotificationController::class, 'index']);
        Route::put('notifications/{id}/read', [NotificationController::class, 'markRead']);
        Route::put('notifications/read-all',  [NotificationController::class, 'markAllRead']);

        // Broker
        Route::prefix('broker')->group(function () {
            Route::get('referrals',  [BrokerController::class, 'referrals']);
            Route::get('earnings',   [BrokerController::class, 'earnings']);
            Route::post('refer',     [BrokerController::class, 'refer']);
        });

        // Driver
        Route::prefix('driver')->group(function () {
            Route::post('register',       [DriverController::class, 'register']);
            Route::get('assignments',     [DriverController::class, 'assignments']);
            Route::put('availability',    [DriverController::class, 'updateAvailability']);
        });

        // Admin routes
        Route::prefix('admin')->middleware('can:admin')->group(function () {
            Route::get('users',                          [AdminUserController::class, 'index']);
            Route::put('users/{id}/toggle-active',       [AdminUserController::class, 'toggleActive']);
            Route::put('users/{id}/role',                [AdminUserController::class, 'updateRole']);
            Route::get('kyc/pending',                    [AdminKycController::class, 'pending']);
            Route::put('kyc/{id}/approve',               [AdminKycController::class, 'approve']);
            Route::put('kyc/{id}/reject',                [AdminKycController::class, 'reject']);
            Route::get('listings',                       [AdminListingController::class, 'index']);
            Route::put('listings/{id}/toggle-active',    [AdminListingController::class, 'toggleActive']);
            Route::get('payments',                       [AdminPaymentController::class, 'index']);
            Route::post('payments/{id}/refund',          [AdminPaymentController::class, 'refund']);
            Route::get('disputes',                       [AdminDisputeController::class, 'index']);
            Route::put('disputes/{id}/resolve',          [AdminDisputeController::class, 'resolve']);
        });
    });
});

// Load additional phase routes
foreach (['api_phase2', 'api_phase3', 'api_phase4'] as $file) {
    $path = __DIR__ . "/{$file}.php";
    if (file_exists($path)) require $path;
}
