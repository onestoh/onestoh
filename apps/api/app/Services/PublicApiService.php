<?php
namespace App\Services;

use App\Jobs\DeliverWebhookJob;
use App\Models\ApiClient;
use App\Models\ApiWebhookDelivery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PublicApiService
{
    public function createClient(User $user, string $name, array $scopes): array
    {
        $clientId     = Str::uuid()->toString();
        $clientSecret = Str::random(40);

        $client = ApiClient::create([
            'user_id'             => $user->id,
            'name'                => $name,
            'client_id'           => $clientId,
            'client_secret_hash'  => Hash::make($clientSecret),
            'scopes'              => $scopes,
            'is_active'           => true,
            'rate_limit_per_minute' => 60,
        ]);

        return [
            'client'        => $client,
            'client_secret' => $clientSecret, // returned ONCE, not stored
        ];
    }

    public function authenticate(string $clientId, string $clientSecret): ?ApiClient
    {
        $client = ApiClient::where('client_id', $clientId)
            ->where('is_active', true)
            ->first();

        if (!$client || !Hash::check($clientSecret, $client->client_secret_hash)) {
            return null;
        }

        return $client;
    }

    public function checkRateLimit(ApiClient $client, Request $request): bool
    {
        $key     = "api_rl:{$client->client_id}:" . floor(time() / 60);
        $current = Cache::increment($key);

        if ($current === 1) {
            Cache::expire($key, 120); // TTL 2 minutes to cover window
        }

        return $current <= $client->rate_limit_per_minute;
    }

    public function dispatchWebhook(ApiClient $client, string $eventType, array $payload): void
    {
        if (!$client->webhook_url) return;

        $payloadJson = json_encode($payload);
        $signature   = hash_hmac('sha256', $payloadJson, $client->webhook_secret ?? '');

        $delivery = ApiWebhookDelivery::create([
            'api_client_id' => $client->id,
            'event_type'    => $eventType,
            'payload'       => $payload,
            'signature'     => $signature,
            'status'        => 'pending',
        ]);

        DeliverWebhookJob::dispatch($delivery);
    }

    public function retryFailedWebhooks(): void
    {
        $deliveries = ApiWebhookDelivery::where('status', 'failed')
            ->where('attempt_count', '<', 5)
            ->where(function ($q) {
                $q->whereNull('next_retry_at')
                  ->orWhere('next_retry_at', '<=', now());
            })
            ->get();

        foreach ($deliveries as $delivery) {
            DeliverWebhookJob::dispatch($delivery);
        }
    }

    public function getAvailableScopes(): array
    {
        return [
            ['scope' => 'listings:read',    'description' => 'Read asset listings and availability'],
            ['scope' => 'listings:write',   'description' => 'Create and update asset listings'],
            ['scope' => 'bookings:read',    'description' => 'Read booking details'],
            ['scope' => 'bookings:write',   'description' => 'Create and manage bookings'],
            ['scope' => 'payments:read',    'description' => 'Read payment history'],
            ['scope' => 'users:read',       'description' => 'Read user profile information'],
            ['scope' => 'analytics:read',   'description' => 'Read analytics and demand forecasts'],
            ['scope' => 'webhooks:manage',  'description' => 'Manage webhook endpoints'],
        ];
    }

    public function rotateSecret(ApiClient $client): string
    {
        $newSecret = Str::random(40);
        $client->update(['client_secret_hash' => Hash::make($newSecret)]);
        return $newSecret;
    }
}
