<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiRateLimit
{
    private array $limits = [
        'auth' => ['requests' => 20, 'window' => 60],    // 20/min for auth endpoints
        'payment' => ['requests' => 30, 'window' => 60], // 30/min for payment endpoints
        'default' => ['requests' => 120, 'window' => 60], // 120/min for everything else
    ];

    public function handle(Request $request, Closure $next, string $type = 'default')
    {
        $limit = $this->limits[$type] ?? $this->limits['default'];
        $identifier = $request->user()?->id ? 'user:' . $request->user()->id : 'ip:' . $request->ip();
        $key = "rate_limit:{$type}:{$identifier}";

        $current = (int) Cache::get($key, 0);

        if ($current >= $limit['requests']) {
            return response()->json([
                'message' => 'Too many requests. Please slow down.',
                'retry_after' => $limit['window'],
            ], 429)->withHeaders([
                'X-RateLimit-Limit' => $limit['requests'],
                'X-RateLimit-Remaining' => 0,
                'Retry-After' => $limit['window'],
            ]);
        }

        Cache::add($key, 0, $limit['window']);
        Cache::increment($key);

        return $next($request)->withHeaders([
            'X-RateLimit-Limit' => $limit['requests'],
            'X-RateLimit-Remaining' => max(0, $limit['requests'] - $current - 1),
        ]);
    }
}
