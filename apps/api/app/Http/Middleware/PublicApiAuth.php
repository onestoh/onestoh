<?php
namespace App\Http\Middleware;

use App\Services\PublicApiService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicApiAuth
{
    public function __construct(private PublicApiService $apiService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $clientId     = $request->header('X-API-Client-Id');
        $clientSecret = $request->header('X-API-Client-Secret');

        if (!$clientId || !$clientSecret) {
            return response()->json([
                'error' => 'API credentials required. Pass X-API-Client-Id and X-API-Client-Secret headers.',
            ], 401);
        }

        $client = $this->apiService->authenticate($clientId, $clientSecret);

        if (!$client) {
            return response()->json(['error' => 'Invalid API credentials'], 401);
        }

        if (!$this->apiService->checkRateLimit($client, $request)) {
            return response()->json([
                'error' => 'Rate limit exceeded. Max ' . $client->rate_limit_per_minute . ' requests per minute.',
            ], 429);
        }

        // Check IP allowlist if configured
        if (!empty($client->allowed_ips)) {
            $clientIp = $request->ip();
            if (!in_array($clientIp, $client->allowed_ips)) {
                return response()->json(['error' => 'IP address not allowed for this API client'], 403);
            }
        }

        $client->increment('total_requests');
        $client->update(['last_used_at' => now()]);
        $request->attributes->set('api_client', $client);

        return $next($request);
    }
}
