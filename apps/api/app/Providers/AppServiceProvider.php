<?php
namespace App\Providers;

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

        $this->configureRateLimiting();
        $this->registerObservers();
    }

    private function configureRateLimiting(): void
    {
        // Auth endpoints — tight limit
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(config('security.rate_limits.auth', 20))
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many authentication attempts. Please try again in a minute.',
                    ], 429);
                });
        });

        // Payment endpoints
        RateLimiter::for('payments', function (Request $request) {
            $key = $request->user()?->id ? 'user:' . $request->user()->id : $request->ip();
            return Limit::perMinute(config('security.rate_limits.payment', 30))
                ->by($key)
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many payment requests. Please slow down.',
                    ], 429);
                });
        });

        // API default
        RateLimiter::for('api', function (Request $request) {
            $key = $request->user()?->id ? 'user:' . $request->user()->id : $request->ip();
            return Limit::perMinute(config('security.rate_limits.api_default', 120))->by($key);
        });

        // Uploads
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?? $request->ip());
        });
    }

    private function registerObservers(): void
    {
        // Observers are registered via ObserverServiceProvider
    }
}
