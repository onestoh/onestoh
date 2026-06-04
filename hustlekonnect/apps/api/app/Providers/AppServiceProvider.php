<?php
namespace App\Providers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SaleOffer;
use App\Observers\BookingObserver;
use App\Observers\PaymentObserver;
use App\Observers\SaleOfferObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Register observers for zero-human-involvement automation
        Booking::observe(BookingObserver::class);
        Payment::observe(PaymentObserver::class);

        if (class_exists(SaleOffer::class)) {
            SaleOffer::observe(SaleOfferObserver::class);
        }

        // Named rate limiters
        RateLimiter::for('auth', fn(Request $r) =>
            Limit::perMinute(config('security.rate_limits.auth', 20))->by($r->ip())
                ->response(fn() => response()->json(['message' => 'Too many auth attempts.'], 429))
        );

        RateLimiter::for('payments', fn(Request $r) =>
            Limit::perMinute(config('security.rate_limits.payment', 30))->by($r->user()?->id ?? $r->ip())
                ->response(fn() => response()->json(['message' => 'Too many payment requests.'], 429))
        );

        RateLimiter::for('api', fn(Request $r) =>
            Limit::perMinute(config('security.rate_limits.api_default', 120))->by($r->user()?->id ?? $r->ip())
        );
    }
}
