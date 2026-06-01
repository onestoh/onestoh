<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeService
{
    private string $secretKey;
    private string $webhookSecret;
    private string $baseUrl = 'https://api.stripe.com/v1';

    public function __construct()
    {
        $this->secretKey     = config('services.stripe.secret', '');
        $this->webhookSecret = config('services.stripe.webhook_secret', '');
    }

    public function createPaymentIntent(Booking $booking, string $currency = 'usd'): array
    {
        $multiCurrencyService = app(MultiCurrencyService::class);
        $currencyUpper = strtoupper($currency);

        // Convert KES amount to target currency
        $amount = $booking->total_amount;
        if ($currencyUpper !== 'KES') {
            $amount = $multiCurrencyService->convert($booking->total_amount, 'KES', $currencyUpper);
        }

        // Stripe expects amounts in smallest currency unit (cents for USD)
        $noDecimalCurrencies = ['UGX', 'TZS'];
        $amountInSmallest = in_array($currencyUpper, $noDecimalCurrencies)
            ? intval($amount)
            : intval($amount * 100);

        $response = Http::withBasicAuth($this->secretKey, '')
            ->asForm()
            ->post("{$this->baseUrl}/payment_intents", [
                'amount'   => $amountInSmallest,
                'currency' => strtolower($currencyUpper),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'platform'   => 'theonlineyard',
                ],
                'description' => 'Booking #' . $booking->id . ' on TheOnlineYard',
            ]);

        if ($response->failed()) {
            Log::error('Stripe PaymentIntent creation failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('Stripe error: ' . ($response->json('error.message') ?? $response->body()));
        }

        return $response->json();
    }

    public function confirmPayment(string $paymentIntentId): bool
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/payment_intents/{$paymentIntentId}");

        if ($response->failed()) return false;

        return $response->json('status') === 'succeeded';
    }

    public function handleWebhook(array $payload, string $signature): void
    {
        // Verify signature
        $body      = json_encode($payload);
        $parts     = explode(',', $signature);
        $timestamp = null;
        $sigHash   = null;

        foreach ($parts as $part) {
            if (str_starts_with($part, 't=')) {
                $timestamp = substr($part, 2);
            } elseif (str_starts_with($part, 'v1=')) {
                $sigHash = substr($part, 3);
            }
        }

        if ($timestamp && $sigHash) {
            $signedPayload = "{$timestamp}.{$body}";
            $expected      = hash_hmac('sha256', $signedPayload, $this->webhookSecret);
            if (!hash_equals($expected, $sigHash)) {
                throw new \RuntimeException('Stripe webhook signature verification failed.');
            }
        }

        $eventType = $payload['type'] ?? null;
        $object    = $payload['data']['object'] ?? [];

        if ($eventType === 'payment_intent.succeeded') {
            $bookingId = $object['metadata']['booking_id'] ?? null;
            if ($bookingId) {
                $booking = \App\Models\Booking::find($bookingId);
                if ($booking) {
                    $booking->update(['status' => 'confirmed', 'payment_status' => 'paid']);
                    Payment::create([
                        'booking_id'     => $booking->id,
                        'amount'         => $object['amount'] / 100,
                        'currency'       => strtoupper($object['currency']),
                        'payment_method' => 'stripe',
                        'reference'      => $object['id'],
                        'status'         => 'completed',
                        'paid_at'        => now(),
                    ]);
                }
            }
        } elseif ($eventType === 'payment_intent.payment_failed') {
            $bookingId = $object['metadata']['booking_id'] ?? null;
            if ($bookingId) {
                \App\Models\Booking::where('id', $bookingId)->update(['payment_status' => 'failed']);
            }
        }

        Log::info('Stripe webhook processed', ['type' => $eventType]);
    }

    public function createRefund(Payment $payment, float $amount): bool
    {
        $refundAmount = intval($amount * 100); // cents
        $response = Http::withBasicAuth($this->secretKey, '')
            ->asForm()
            ->post("{$this->baseUrl}/refunds", [
                'payment_intent' => $payment->reference,
                'amount'         => $refundAmount,
                'reason'         => 'requested_by_customer',
            ]);

        if ($response->failed()) {
            Log::error('Stripe refund failed', ['payment_id' => $payment->id, 'error' => $response->json('error.message')]);
            return false;
        }

        $payment->update(['status' => 'refunded']);
        return true;
    }

    public function getSupportedCurrencies(): array
    {
        return ['USD', 'GBP', 'EUR', 'KES', 'UGX', 'TZS'];
    }
}
