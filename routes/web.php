<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertySaveController;
use App\Http\Controllers\RentPaymentController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\EscrowController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelRoomController;
use App\Http\Controllers\PricingRuleController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\PropertyModerationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

// PUBLIC ROUTES
Route::get('/', [HomeController::class, 'index'])->name('home');

// Marketplace
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
Route::get('/marketplace/listing/{id}', [MarketplaceController::class, 'show'])->name('listing.show');
Route::get('/commercial', fn() => redirect('/marketplace?type=commercial'));
Route::get('/financing', [HomeController::class, 'financing'])->name('financing');
Route::get('/verification', [HomeController::class, 'verification'])->name('verification');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Auctions
Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions');
Route::get('/auctions/{id}', [AuctionController::class, 'show'])->name('auction.show');

// Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.post');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:3,1')
    ->name('register.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Referral tracking (public)
Route::get('/ref/{code}', [ReferralController::class, 'track'])->name('referral.track');

// M-Pesa callback (no CSRF, public endpoint)
Route::post('/rent/mpesa/callback', [RentPaymentController::class, 'mpesaCallback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('rent.mpesa.callback');

// PROTECTED ROUTES
Route::middleware(['auth.session'])->group(function () {

    // Dashboard (all roles)
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
        Route::get('/landlord', [DashboardController::class, 'landlord'])->name('dashboard.landlord');
        Route::get('/broker', [DashboardController::class, 'broker'])->name('dashboard.broker');
        Route::get('/promoter', [DashboardController::class, 'promoter'])->name('dashboard.promoter');
        Route::get('/tenant', [DashboardController::class, 'tenant'])->name('dashboard.tenant');
        Route::get('/developer', [DashboardController::class, 'developer'])->name('dashboard.developer');
        Route::get('/valuer', [DashboardController::class, 'valuer'])->name('dashboard.valuer');
        Route::get('/surveyor', [DashboardController::class, 'surveyor'])->name('dashboard.surveyor');
        Route::get('/auctioneer', [DashboardController::class, 'auctioneer'])->name('dashboard.auctioneer');
        Route::get('/investor', [DashboardController::class, 'investor'])->name('dashboard.investor');
        Route::get('/corporate', [DashboardController::class, 'corporate'])->name('dashboard.corporate');
        Route::get('/property-manager', [DashboardController::class, 'propertyManager'])->name('dashboard.property-manager');
        Route::get('/finance', [DashboardController::class, 'finance'])->name('dashboard.finance');
        Route::get('/messages', [DashboardController::class, 'messages'])->name('dashboard.messages');
        Route::get('/notifications', [DashboardController::class, 'notifications'])->name('dashboard.notifications');
        Route::get('/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
        Route::get('/verification', [DashboardController::class, 'verification'])->name('dashboard.verification');
    });

    // Properties
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{id}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{id}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    // Property Save (toggle)
    Route::post('/properties/{id}/save', [PropertySaveController::class, 'toggle'])->name('properties.save');

    // Rent Payments
    Route::post('/rent/pay', [RentPaymentController::class, 'initiate'])->name('rent.pay');
    Route::get('/rent/history/{leaseId}', [RentPaymentController::class, 'history'])->name('rent.history');

    // Auctions — Bidding
    Route::post('/auctions/{id}/bid', [BidController::class, 'place'])->name('auctions.bid');

    // Inspections
    Route::post('/inspections', [InspectionController::class, 'book'])->name('inspections.book');

    // Maintenance
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');

    // Messages
    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/conversation/{userId}', [MessageController::class, 'conversation'])->name('messages.conversation');

    // Verification
    Route::post('/verification/submit', [VerificationController::class, 'submit'])->name('verification.submit');

    // Referrals
    Route::post('/referrals/generate', [ReferralController::class, 'generate'])->name('referrals.generate');

    // PDF Generation
    Route::get('/pdf/lease/{id}', [PdfController::class, 'leaseAgreement'])->name('pdf.lease');
    Route::get('/pdf/receipt/{id}', [PdfController::class, 'rentReceipt'])->name('pdf.receipt');
    Route::get('/pdf/statement/{userId}/{month}', [PdfController::class, 'ownerStatement'])
        ->middleware('role:landlord,admin')
        ->name('pdf.statement');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Escrow
    Route::post('/escrow', [EscrowController::class, 'initiate'])->name('escrow.initiate');
    Route::put('/escrow/{id}/release', [EscrowController::class, 'release'])->name('escrow.release');
    Route::put('/escrow/{id}/dispute', [EscrowController::class, 'dispute'])->name('escrow.dispute');

    // Admin workflows
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/kyc', [KycController::class, 'index'])->name('admin.kyc.index');
        Route::post('/kyc/{id}/approve', [KycController::class, 'approve'])->name('admin.kyc.approve');
        Route::post('/kyc/{id}/reject', [KycController::class, 'reject'])->name('admin.kyc.reject');

        Route::get('/properties', [PropertyModerationController::class, 'index'])->name('admin.properties.index');
        Route::post('/properties/{id}/approve', [PropertyModerationController::class, 'approve'])->name('admin.properties.approve');
        Route::post('/properties/{id}/suspend', [PropertyModerationController::class, 'suspend'])->name('admin.properties.suspend');
        Route::post('/properties/{id}/feature', [PropertyModerationController::class, 'feature'])->name('admin.properties.feature');

        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
        Route::post('/users/{id}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('admin.users.toggleActive');
    });
});

// Search suggestions (public, no auth)
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// BOOKING — public endpoints (no auth)
Route::get('/bookings/availability/{propertyId}', [BookingController::class, 'blockedDates']);
Route::get('/api/search/availability', [BookingController::class, 'checkAvailability']);

// BOOKING — auth required
Route::middleware(['auth.session'])->group(function () {
    Route::get('/properties/{id}/book', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/bookings/{id}/payment', [BookingController::class, 'payment'])->name('booking.payment');
    Route::post('/bookings/{id}/payment', [BookingController::class, 'processPayment'])->name('booking.pay');
    Route::get('/bookings/{id}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('/dashboard/bookings', [BookingController::class, 'myBookings'])->name('dashboard.bookings');
    Route::get('/dashboard/host-bookings', [BookingController::class, 'myHostBookings'])->name('dashboard.host-bookings');
    Route::post('/bookings/{id}/review', [BookingController::class, 'storeReview'])->name('booking.review');
    Route::post('/bookings/{id}/checkin', [BookingController::class, 'checkIn'])->name('booking.checkin');
    Route::post('/bookings/{id}/checkout', [BookingController::class, 'checkOut'])->name('booking.checkout');

    // Hotel rooms (owner)
    Route::get('/properties/{propertyId}/rooms', [HotelRoomController::class, 'index']);
    Route::post('/properties/{propertyId}/rooms', [HotelRoomController::class, 'store']);
    Route::put('/properties/{propertyId}/rooms/{roomId}', [HotelRoomController::class, 'update']);
    Route::delete('/properties/{propertyId}/rooms/{roomId}', [HotelRoomController::class, 'destroy']);

    // Pricing rules
    Route::get('/properties/{propertyId}/pricing', [PricingRuleController::class, 'index']);
    Route::post('/properties/{propertyId}/pricing', [PricingRuleController::class, 'store']);
    Route::delete('/properties/{propertyId}/pricing/{ruleId}', [PricingRuleController::class, 'destroy']);
});
