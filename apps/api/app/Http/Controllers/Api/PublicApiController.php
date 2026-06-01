<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Services\PublicApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicApiController extends Controller
{
    public function __construct(private PublicApiService $service) {}

    public function index(Request $request): JsonResponse
    {
        $clients = ApiClient::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $clients]);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'scopes'         => 'required|array|min:1',
            'scopes.*'       => 'string',
            'webhook_url'    => 'nullable|url',
            'allowed_ips'    => 'nullable|array',
            'allowed_ips.*'  => 'ip',
            'rate_limit_per_minute' => 'nullable|integer|min:10|max:1000',
        ]);

        $result = $this->service->createClient(
            $request->user(),
            $validated['name'],
            $validated['scopes']
        );

        $client = $result['client'];

        if (isset($validated['webhook_url'])) {
            $client->update(['webhook_url' => $validated['webhook_url']]);
        }
        if (isset($validated['allowed_ips'])) {
            $client->update(['allowed_ips' => $validated['allowed_ips']]);
        }
        if (isset($validated['rate_limit_per_minute'])) {
            $client->update(['rate_limit_per_minute' => $validated['rate_limit_per_minute']]);
        }

        return response()->json([
            'data'          => $client->fresh(),
            'client_secret' => $result['client_secret'], // shown ONCE
            'message'       => 'Store your client_secret securely. It will not be shown again.',
        ], 201);
    }

    public function delete(Request $request, ApiClient $client): JsonResponse
    {
        if ($client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $client->delete();
        return response()->json(['message' => 'API client deleted']);
    }

    public function rotateSecret(Request $request, ApiClient $client): JsonResponse
    {
        if ($client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $newSecret = $this->service->rotateSecret($client);

        return response()->json([
            'client_secret' => $newSecret,
            'message'       => 'Secret rotated. Store the new secret securely.',
        ]);
    }

    public function webhookDeliveries(Request $request, ApiClient $client): JsonResponse
    {
        if ($client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $deliveries = $client->webhookDeliveries()
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(['data' => $deliveries]);
    }

    public function scopes(): JsonResponse
    {
        return response()->json(['scopes' => $this->service->getAvailableScopes()]);
    }
}
