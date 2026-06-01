<?php
namespace App\Services;

use App\Models\{Booking, PaystackTransaction};
use Illuminate\Support\Facades\{Http, Log};
use Illuminate\Support\Str;

class PaystackService
{
    private string $secretKey;
    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key', env('PAYSTACK_SECRET_KEY'));
    }

    /**
     * Initiate a Paystack payment for a booking.
     * Returns ['authorization_url', 'access_code', 'reference'].
     */
    public function initiatePayment(Booking $booking, string $email, string $currency = 'NGN'): array
    {
        $reference = 'TOY-' . Str::upper(Str::random(12));
        $amountKobo = (int) bcmul((string) $booking->total_amount, '100');

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email'     => $email,
                'amount'    => $amountKobo,
                'currency'  => $currency,
                'reference' => $reference,
                'metadata'  => [
                    'booking_id' => (string) $booking->id,
                    'user_id'    => (string) $booking->renter_id,
                ],
            ]);

        abort_if(!$response->successful(), 502, 'Paystack initialisation failed: ' . $response->body());

        $data = $response->json('data');

        PaystackTransaction::create([
            'booking_id'   => $booking->id,
            'user_id'      => $booking->renter_id,
            'reference'    => $reference,
            'access_code'  => $data['access_code'],
            'amount_kobo'  => $amountKobo,
            'currency'     => $currency,
            'status'       => 'pending',
        ]);

        return [
            'authorization_url' => $data['authorization_url'],
            'access_code'       => $data['access_code'],
            'reference'         => $reference,
        ];
    }

    /**
     * Verify a Paystack payment by reference.
     */
    public function verifyPayment(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        abort_if(!$response->successful(), 502, 'Paystack verification failed.');

        $data   = $response->json('data');
        $status = $data['status']; // 'success', 'failed', 'abandoned'

        PaystackTransaction::where('reference', $reference)
            ->update([
                'status'           => $status,
                'gateway_response' => $data,
                'paid_at'          => $status === 'success' ? now() : null,
            ]);

        return [
            'status'    => $status,
            'amount'    => $data['amount'] / 100,
            'currency'  => $data['currency'],
            'reference' => $reference,
            'paid_at'   => $data['paid_at'] ?? null,
        ];
    }

    /**
     * Validate webhook signature and process charge.success events.
     */
    public function handleWebhook(array $payload, string $signature): void
    {
        $computed = hash_hmac('sha512', json_encode($payload), $this->secretKey);
        abort_if($computed !== $signature, 401, 'Invalid Paystack webhook signature.');

        $event     = $payload['event'] ?? '';
        $data      = $payload['data'] ?? [];
        $reference = $data['reference'] ?? null;

        if ($event === 'charge.success' && $reference) {
            $tx = PaystackTransaction::where('reference', $reference)->first();
            if ($tx && $tx->status !== 'success') {
                $tx->update([
                    'status'           => 'success',
                    'gateway_response' => $data,
                    'paid_at'          => now(),
                ]);

                // Mark booking payment as paid
                Booking::where('id', $tx->booking_id)->update(['payment_status' => 'paid']);
            }
        }

        if ($event === 'charge.failed' && $reference) {
            PaystackTransaction::where('reference', $reference)->update(['status' => 'failed', 'gateway_response' => $data]);
        }
    }

    /**
     * Initiate a transfer (payout) to an owner's bank account.
     */
    public function initiatePayout(float $amount, string $bankCode, string $accountNumber, string $currency = 'NGN'): array
    {
        // Step 1: Create transfer recipient
        $recipientResp = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transferrecipient", [
                'type'           => 'nuban',
                'account_number' => $accountNumber,
                'bank_code'      => $bankCode,
                'currency'       => $currency,
            ]);

        abort_if(!$recipientResp->successful(), 502, 'Failed to create transfer recipient.');
        $recipientCode = $recipientResp->json('data.recipient_code');

        // Step 2: Initiate transfer
        $transferResp = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transfer", [
                'source'    => 'balance',
                'amount'    => (int) bcmul((string) $amount, '100'),
                'recipient' => $recipientCode,
                'reason'    => 'TheOnlineYard payout',
                'currency'  => $currency,
            ]);

        abort_if(!$transferResp->successful(), 502, 'Transfer initiation failed.');

        return [
            'transfer_code' => $transferResp->json('data.transfer_code'),
            'status'        => $transferResp->json('data.status'),
            'reference'     => $transferResp->json('data.reference'),
        ];
    }

    /**
     * List available banks for a given country.
     */
    public function getBanks(string $country = 'nigeria'): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/bank", ['country' => $country, 'perPage' => 100]);

        return $response->successful() ? $response->json('data') : [];
    }
}
