<?php
namespace App\Jobs;

use App\Models\ApiWebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $maxExceptions = 5;

    public function __construct(private ApiWebhookDelivery $delivery) {}

    public function handle(): void
    {
        $client = $this->delivery->apiClient;

        if (!$client || !$client->webhook_url) return;

        $this->delivery->increment('attempt_count');

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type'       => 'application/json',
                    'X-YardOS-Event'     => $this->delivery->event_type,
                    'X-YardOS-Signature' => $this->delivery->signature,
                    'X-YardOS-Delivery'  => (string) $this->delivery->id,
                ])
                ->post($client->webhook_url, $this->delivery->payload);

            $this->delivery->update([
                'response_status' => $response->status(),
                'response_body'   => substr($response->body(), 0, 500),
                'status'          => $response->successful() ? 'delivered' : 'failed',
                'delivered_at'    => $response->successful() ? now() : null,
            ]);

            if (!$response->successful()) {
                $this->scheduleRetry();
            }
        } catch (\Exception $e) {
            $this->delivery->update([
                'response_body' => $e->getMessage(),
                'status'        => 'failed',
            ]);
            $this->scheduleRetry();
        }
    }

    private function scheduleRetry(): void
    {
        $attempt = $this->delivery->attempt_count;
        if ($attempt >= 5) {
            $this->delivery->update(['status' => 'abandoned']);
            return;
        }
        $delaySeconds = pow(2, $attempt) * 60; // 2min, 4min, 8min, 16min, 32min
        $this->delivery->update(['next_retry_at' => now()->addSeconds($delaySeconds)]);
        self::dispatch($this->delivery)->delay(now()->addSeconds($delaySeconds));
    }
}
