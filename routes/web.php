<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;

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
