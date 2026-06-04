<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiRateLimit
{
    private array $limits = ['auth' => [20, 60], 'payment' => [30, 60], 'default' => [120, 60]];

    public function handle(Request $request, Closure $next, string $type = 'default')
    {
        [$max, $window] = $this->limits[$type] ?? $this->limits['default'];
        $id = $request->user()?->id ? 'u:'.$request->user()->id : 'ip:'.$request->ip();
        $key = "rl:{$type}:{$id}";
        $current = (int) Cache::get($key, 0);
        if ($current >= $max) {
            return response()->json(['message' => 'Too many requests.', 'retry_after' => $window], 429)
                ->withHeaders(['X-RateLimit-Limit' => $max, 'X-RateLimit-Remaining' => 0, 'Retry-After' => $window]);
        }
        Cache::add($key, 0, $window);
        Cache::increment($key);
        return $next($request)->withHeaders(['X-RateLimit-Limit' => $max, 'X-RateLimit-Remaining' => max(0, $max - $current - 1)]);
    }
}
