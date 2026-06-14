<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BrokerController;
use App\Http\Controllers\YardController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminKycController;
use App\Http\Controllers\Admin\AdminListingController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminDisputeController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminYardController;
use App\Http\Controllers\Admin\AdminPayoutController;
use App\Http\Controllers\Admin\AdminCommissionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\MfaController;

// AUTH
Auth::routes();

// MFA — must be accessible without being logged in
Route::get('/mfa/verify', [MfaController::class, 'showVerify'])->name('mfa.verify');
Route::post('/mfa/verify', [MfaController::class, 'verify'])->name('mfa.verify.post');
Route::post('/mfa/send', [MfaController::class, 'sendCode'])->name('mfa.send');

// PUBLIC
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
Route::get('/marketplace/{slug}', [MarketplaceController::class, 'show'])->name('listings.show');
Route::get('/listings/{listing}/availability', [ListingController::class, 'availability'])->name('listings.availability');
Route::get('/broker/register', [BrokerController::class, 'registerPage'])->name('broker.register');
Route::get('/yard/{slug}', [YardController::class, 'publicProfile'])->name('yard.profile');

// AUTHENTICATED
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('/kyc/pending', [KycController::class, 'pending'])->name('kyc.pending');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/payout', [WalletController::class, 'requestPayout'])->name('wallet.payout');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{booking}', [MessageController::class, 'thread'])->name('messages.thread');
    Route::post('/messages/{booking}', [MessageController::class, 'send'])->name('messages.send');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::middleware('verified.account')->group(function () {
        Route::resource('listings', ListingController::class)->except(['show']);
        Route::post('/listings/{listing}/photos', [ListingController::class, 'uploadPhotos'])->name('listings.photos');
        Route::delete('/listings/{listing}/photos/{photo}', [ListingController::class, 'deletePhoto'])->name('listings.photos.delete');
        Route::post('/listings/{listing}/block-dates', [ListingController::class, 'blockDates'])->name('listings.block-dates');

        Route::get('/bookings', [BookingController::class, 'index'])->name('client.bookings');
        Route::get('/book/{listing}', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/bookings/{booking}/start', [BookingController::class, 'markStarted'])->name('bookings.start');
        Route::post('/bookings/{booking}/complete', [BookingController::class, 'markCompleted'])->name('bookings.complete');
        Route::post('/bookings/{booking}/dispute', [BookingController::class, 'raiseDispute'])->name('bookings.dispute');
        Route::get('/bookings/{booking}/pay', [BookingController::class, 'payment'])->name('bookings.pay');
        Route::post('/bookings/{booking}/mpesa', [BookingController::class, 'initiateMpesa'])->name('bookings.mpesa');
        Route::post('/bookings/{booking}/review', [BookingController::class, 'submitReview'])->name('bookings.review');
    });

    Route::get('/saved-listings', [ClientController::class, 'saved'])->name('client.saved');

    Route::middleware('role:yard_owner,individual_owner')->group(function () {
        Route::get('/yard', [YardController::class, 'index'])->name('yard.index');
        Route::get('/yard/create', [YardController::class, 'create'])->name('yard.create');
        Route::post('/yard', [YardController::class, 'store'])->name('yard.store');
        Route::get('/yard/edit', [YardController::class, 'edit'])->name('yard.edit');
        Route::put('/yard', [YardController::class, 'update'])->name('yard.update');
        Route::get('/yard/bookings', [YardController::class, 'bookings'])->name('yard.bookings');
        Route::get('/yard/drivers', [YardController::class, 'drivers'])->name('yard.drivers');
        Route::post('/yard/drivers', [YardController::class, 'storeDriver'])->name('yard.drivers.store');
        Route::get('/yard/analytics', [YardController::class, 'analytics'])->name('yard.analytics');
    });

    Route::middleware('role:broker')->group(function () {
        Route::get('/broker/dashboard', [BrokerController::class, 'dashboard'])->name('broker.dashboard');
        Route::get('/broker/commissions', [BrokerController::class, 'commissions'])->name('broker.commissions');
    });

    Route::middleware('role:operator')->group(function () {
        Route::get('/operator/assignments', [OperatorController::class, 'assignments'])->name('operator.assignments');
        Route::post('/operator/assignments/{booking}/update', [OperatorController::class, 'updateStatus'])->name('operator.status');
    });

    Route::post('/payments/mpesa/callback', [BookingController::class, 'mpesaCallback'])->name('payments.mpesa.callback');

    // Wallet top-up
    Route::post('/wallet/topup', [WalletController::class, 'topup'])->name('wallet.topup');
    Route::post('/wallet/topup/callback', [WalletController::class, 'topupCallback'])->name('wallet.topup.callback')->withoutMiddleware(['auth', 'verified']);

    // Reviews
    Route::post('/bookings/{booking}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// ADMIN
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');

    Route::get('kyc', [AdminKycController::class, 'index'])->name('kyc.index');
    Route::post('kyc/{document}/approve', [AdminKycController::class, 'approve'])->name('kyc.approve');
    Route::post('kyc/{document}/reject', [AdminKycController::class, 'reject'])->name('kyc.reject');

    Route::resource('yards', AdminYardController::class);
    Route::post('yards/{yard}/approve', [AdminYardController::class, 'approve'])->name('yards.approve');

    Route::resource('listings', AdminListingController::class);
    Route::post('listings/{listing}/approve', [AdminListingController::class, 'approve'])->name('listings.approve');
    Route::post('listings/{listing}/feature', [AdminListingController::class, 'toggleFeature'])->name('listings.feature');

    Route::resource('bookings', AdminBookingController::class)->only(['index','show']);
    Route::post('bookings/{booking}/release-escrow', [AdminBookingController::class, 'releaseEscrow'])->name('bookings.escrow');

    Route::resource('payments', AdminPaymentController::class)->only(['index','show']);

    Route::resource('disputes', AdminDisputeController::class);
    Route::post('disputes/{dispute}/rule', [AdminDisputeController::class, 'rule'])->name('disputes.rule');
    Route::post('disputes/{dispute}/resolve', [AdminDisputeController::class, 'resolve'])->name('disputes.resolve');

    Route::get('payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
    Route::post('payouts/{payout}/approve', [AdminPayoutController::class, 'approve'])->name('payouts.approve');
    Route::post('payouts/{payout}/reject', [AdminPayoutController::class, 'reject'])->name('payouts.reject');

    Route::get('commissions', [AdminCommissionController::class, 'index'])->name('commissions.index');

    Route::resource('categories', AdminCategoryController::class)->names('categories');

    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);
