<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MtnMomoService
{
    private string $environment;
    private string $subscriptionKey;
    private string $apiUser;
    private string $apiKey;
    private string $baseUrl;
    private string $callbackUrl;

    public function __construct()
    {
        $this->environment     = config('services.mtn_momo.environment', 'sandbox');
        $this->subscriptionKey = config('services.mtn_momo.subscription_key', '');
        $this->apiUser         = config('services.mtn_momo.api_user', '');
        $this->apiKey          = config('services.mtn_momo.api_key', '');
        $this->baseUrl         = config('services.mtn_momo.collections_base_url', 'https://sandbox.momodeveloper.mtn.com');
        $this->callbackUrl     = config('services.mtn_momo.callback_url', '');
    }

    public function initiatePayment(Booking $booking, string $mobileNumber): array
    {
        $token     = $this->getAccessToken('collection');
        $requestId = Str::uuid()->toString();
        $amount    = (string) intval($booking->total_amount);

        $response = Http::withHeaders([
            'Authorization'             => "Bearer {$token}",
            'X-Reference-Id'            => $requestId,
            'X-Target-Environment'      => $this->environment,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Content-Type'              => 'application/json',
        ])->post("{$this->baseUrl}/collection/v1_0/requesttopay", [
            'amount'     => $amount,
            'currency'   => 'UGX',
            'externalId' => (string) $booking->id,
            'payer'      => [
                'partyIdType' => 'MSISDN',
                'partyId'     => $mobileNumber,
            ],
            'payerMessage' => 'Payment for booking #' . $booking->id . ' on TheOnlineYard',
            'payeeNote'    => 'YardOS booking payment',
            'callbackUrl'  => $this->callbackUrl,
        ]);

        if ($response->status() !== 202) {
            Log::error('MTN MoMo initiate payment failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('MTN MoMo payment initiation failed: ' . $response->body());
        }

        return [
            'request_id' => $requestId,
            'status'     => 'pending',
            'message'    => 'Payment request sent to ' . $mobileNumber . '. Please approve on your phone.',
        ];
    }

    public function checkPaymentStatus(string $requestId): array
    {
        $token = $this->getAccessToken('collection');

        $response = Http::withHeaders([
            'Authorization'             => "Bearer {$token}",
            'X-Target-Environment'      => $this->environment,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
        ])->get("{$this->baseUrl}/collection/v1_0/requesttopay/{$requestId}");

        if ($response->failed()) {
            throw new \RuntimeException('MTN MoMo status check failed: ' . $response->body());
        }

        return $response->json();
    }

    public function handleCallback(array $payload): void
    {
        $externalId = $payload['externalId'] ?? null;
        $status     = $payload['status'] ?? null;

        if (!$externalId) return;

        $booking = Booking::find($externalId);
        if (!$booking) return;

        if ($status === 'SUCCESSFUL') {
            $booking->update(['status' => 'confirmed', 'payment_status' => 'paid']);

            Payment::create([
                'booking_id'     => $booking->id,
                'amount'         => $payload['amount'] ?? $booking->total_amount,
                'currency'       => $payload['currency'] ?? 'UGX',
                'payment_method' => 'mtn_momo',
                'reference'      => $payload['financialTransactionId'] ?? null,
                'status'         => 'completed',
                'paid_at'        => now(),
            ]);
        } elseif ($status === 'FAILED') {
            $booking->update(['payment_status' => 'failed']);
        }

        Log::info('MTN MoMo callback processed', ['booking_id' => $booking->id, 'status' => $status]);
    }

    public function initiatePayout(Wallet $wallet, float $amount, string $mobileNumber): array
    {
        $token     = $this->getAccessToken('disbursement');
        $requestId = Str::uuid()->toString();

        $response = Http::withHeaders([
            'Authorization'             => "Bearer {$token}",
            'X-Reference-Id'            => $requestId,
            'X-Target-Environment'      => $this->environment,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Content-Type'              => 'application/json',
        ])->post("{$this->baseUrl}/disbursement/v1_0/transfer", [
            'amount'     => (string) intval($amount),
            'currency'   => 'UGX',
            'externalId' => 'wallet-' . $wallet->id . '-' . time(),
            'payee'      => [
                'partyIdType' => 'MSISDN',
                'partyId'     => $mobileNumber,
            ],
            'payerMessage' => 'TheOnlineYard owner payout',
            'payeeNote'    => 'Rental earnings payout',
        ]);

        if ($response->status() !== 202) {
            throw new \RuntimeException('MTN MoMo payout failed: ' . $response->body());
        }

        return ['request_id' => $requestId, 'status' => 'pending'];
    }

    public function getAccessToken(string $product = 'collection'): string
    {
        $cacheKey = "mtn_momo_token_{$product}";

        return Cache::remember($cacheKey, now()->addMinutes(55), function () use ($product) {
            $url      = "{$this->baseUrl}/{$product}/token/";
            $response = Http::withBasicAuth($this->apiUser, $this->apiKey)
                ->withHeaders(['Ocp-Apim-Subscription-Key' => $this->subscriptionKey])
                ->post($url);

            if ($response->failed()) {
                throw new \RuntimeException('MTN MoMo token fetch failed: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }
}
