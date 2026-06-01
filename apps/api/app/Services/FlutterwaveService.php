<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    private string $secretKey;
    private string $baseUrl = 'https://api.flutterwave.com/v3';

    public function __construct()
    {
        $this->secretKey = config('services.flutterwave.secret_key', '');
    }

    /**
     * Initiate a Flutterwave payment link for a booking.
     */
    public function initiatePayment(Booking $booking, array $cardData, string $redirectUrl): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/payments", [
                'tx_ref'       => 'TOY-' . $booking->id . '-' . time(),
                'amount'       => (float) $booking->total_price,
                'currency'     => 'KES',
                'redirect_url' => $redirectUrl,
                'meta'         => ['booking_id' => $booking->id],
                'customer'     => [
                    'email'      => $booking->client->email,
                    'name'       => $booking->client->name,
                    'phonenumber'=> $booking->client->phone ?? '',
                ],
                'customizations' => [
                    'title'       => 'TheOnlineYard Booking',
                    'description' => 'Booking #' . $booking->id,
                ],
                'payment_options' => 'card',
                'card_number'     => $cardData['card_number'] ?? null,
                'cvv'             => $cardData['cvv'] ?? null,
                'expiry_month'    => $cardData['expiry_month'] ?? null,
                'expiry_year'     => $cardData['expiry_year'] ?? null,
            ]);

        if (!$response->successful()) {
            Log::error('Flutterwave initiate failed', $response->json());
            abort(502, 'Payment gateway error.');
        }

        return $response->json('data');
    }

    /**
     * Verify a Flutterwave transaction by reference.
     */
    public function verifyPayment(string $transactionId): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transactions/{$transactionId}/verify");

        if (!$response->successful()) {
            abort(502, 'Could not verify payment.');
        }

        return $response->json('data');
    }

    /**
     * Handle incoming Flutterwave webhook — validate signature and update booking/payment.
     */
    public function handleWebhook(array $payload): void
    {
        $hash = $payload['verif-hash'] ?? '';
        if ($hash !== config('services.flutterwave.webhook_hash')) {
            abort(401, 'Invalid webhook signature.');
        }

        $event = $payload['event'] ?? '';
        $data  = $payload['data'] ?? [];

        if ($event === 'charge.completed' && ($data['status'] ?? '') === 'successful') {
            $bookingId = $data['meta']['booking_id'] ?? null;

            if ($bookingId && $booking = Booking::find($bookingId)) {
                $booking->update(['status' => 'confirmed']);

                Payment::updateOrCreate(
                    ['booking_id' => $booking->id, 'provider' => 'flutterwave'],
                    [
                        'amount'         => $data['amount'],
                        'currency'       => $data['currency'],
                        'status'         => 'paid',
                        'transaction_id' => $data['id'],
                        'provider_ref'   => $data['flw_ref'] ?? null,
                        'paid_at'        => now(),
                    ]
                );

                $booking->client->notify(new \App\Notifications\BookingConfirmedNotification($booking));
            }
        }
    }

    /**
     * Initiate a refund via Flutterwave.
     */
    public function refund(Payment $payment, float $amount, string $reason): bool
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transactions/{$payment->transaction_id}/refund", [
                'amount'   => $amount,
                'comments' => $reason,
            ]);

        if ($response->successful()) {
            $payment->update(['status' => 'refunded']);
            return true;
        }

        Log::error('Flutterwave refund failed', $response->json());
        return false;
    }

    /**
     * Return currencies supported by Flutterwave that are relevant to this platform.
     */
    public function getSupportedCurrencies(): array
    {
        return ['KES', 'UGX', 'TZS', 'NGN', 'GHS', 'USD'];
    }
}
