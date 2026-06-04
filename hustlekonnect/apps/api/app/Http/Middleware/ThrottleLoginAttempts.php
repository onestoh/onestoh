<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ThrottleLoginAttempts
{
    private const PROGRESSIVE = [5 => 15, 10 => 60, 20 => 1440];

    public function handle(Request $request, Closure $next)
    {
        $key = 'login_attempts:' . md5(strtolower($request->input('email','')).'|'.$request->ip());
        $lockKey = 'login_locked:' . md5(strtolower($request->input('email','')).'|'.$request->ip());

        if (Cache::has($lockKey)) {
            return response()->json(['message' => 'Too many login attempts. Please try again later.', 'retry_after' => Cache::get($lockKey.':ttl', 900)], 429);
        }

        $response = $next($request);

        if (in_array($response->status(), [401, 422])) {
            $attempts = (int) Cache::get($key, 0) + 1;
            Cache::put($key, $attempts, now()->addHours(24));
            foreach (self::PROGRESSIVE as $threshold => $minutes) {
                if ($attempts >= $threshold) {
                    Cache::put($lockKey, true, now()->addMinutes($minutes));
                    Cache::put($lockKey.':ttl', $minutes * 60, now()->addMinutes($minutes));
                    Log::warning('Login lockout', ['ip' => $request->ip(), 'attempts' => $attempts, 'minutes' => $minutes]);
                }
            }
        } elseif ($response->status() === 200) {
            Cache::forget($key);
            Cache::forget($lockKey);
        }
        return $response;
    }
}
