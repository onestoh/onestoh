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
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
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
});
