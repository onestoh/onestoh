<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ThrottleLoginAttempts
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;
    private const PROGRESSIVE_LOCKOUT = [5 => 15, 10 => 60, 20 => 1440]; // attempts => lockout minutes

    public function handle(Request $request, Closure $next)
    {
        $key = 'login_attempts:' . $this->resolveKey($request);
        $attempts = (int) Cache::get($key, 0);

        $lockoutKey = 'login_locked:' . $this->resolveKey($request);
        if (Cache::has($lockoutKey)) {
            $remaining = Cache::get($lockoutKey . ':ttl', self::LOCKOUT_MINUTES * 60);
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.',
                'retry_after' => $remaining,
            ], 429);
        }

        $response = $next($request);

        if ($response->status() === 401 || ($response->status() === 422 && str_contains((string) $response->content(), 'credentials'))) {
            $newAttempts = $attempts + 1;
            Cache::put($key, $newAttempts, now()->addHours(24));

            $lockoutMinutes = $this->getLockoutDuration($newAttempts);
            if ($lockoutMinutes > 0) {
                Cache::put($lockoutKey, true, now()->addMinutes($lockoutMinutes));
                Cache::put($lockoutKey . ':ttl', $lockoutMinutes * 60, now()->addMinutes($lockoutMinutes));

                Log::warning('Account locked due to brute force', [
                    'key' => $this->resolveKey($request),
                    'attempts' => $newAttempts,
                    'lockout_minutes' => $lockoutMinutes,
                    'ip' => $request->ip(),
                ]);
            }
        } elseif ($response->status() === 200) {
            Cache::forget($key);
            Cache::forget($lockoutKey);
        }

        return $response;
    }

    private function resolveKey(Request $request): string
    {
        $email = strtolower($request->input('email', ''));
        $ip = $request->ip();
        return md5($email . '|' . $ip);
    }

    private function getLockoutDuration(int $attempts): int
    {
        $duration = 0;
        foreach (self::PROGRESSIVE_LOCKOUT as $threshold => $minutes) {
            if ($attempts >= $threshold) {
                $duration = $minutes;
            }
        }
        return $duration;
    }
}
