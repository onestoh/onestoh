<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Booking;

class MpesaService
{
    private Client $client;
    private string $consumerKey;
    private string $consumerSecret;
    private string $passKey;
    private string $shortCode;
    private string $b2cInitiatorName;
    private string $b2cSecurityCredential;
    private string $b2cShortCode;
    private string $baseUrl;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->passKey = config('services.mpesa.pass_key');
        $this->shortCode = config('services.mpesa.shortcode');
        $this->b2cInitiatorName = config('services.mpesa.b2c_initiator_name');
        $this->b2cSecurityCredential = config('services.mpesa.b2c_security_credential');
        $this->b2cShortCode = config('services.mpesa.b2c_shortcode');
        $this->baseUrl = config('services.mpesa.sandbox', true)
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }

    /**
     * Get OAuth token, cached for 55 minutes.
     */
    public function getAccessToken(): string
    {
        return Cache::remember('mpesa_access_token', 3300, function () {
            $credentials = base64_encode("{$this->consumerKey}:{$this->consumerSecret}");
            $response = $this->client->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials", [
                'headers' => ['Authorization' => "Basic {$credentials}"],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'];
        });
    }

    /**
     * Initiate STK Push (Lipa Na M-Pesa Online).
     */
    public function stkPush(string $phone, float $amount, string $bookingId, string $accountRef, string $description): array
    {
        $token = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortCode . $this->passKey . $timestamp);

        $phone = $this->formatPhone($phone);

        $payload = [
            'BusinessShortCode' => $this->shortCode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => (int) ceil($amount),
            'PartyA'            => $phone,
            'PartyB'            => $this->shortCode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => route('mpesa.stk.callback'),
            'AccountReference'  => $accountRef,
            'TransactionDesc'   => $description,
        ];

        try {
            $response = $this->client->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('MPesa STK Push initiated', ['booking_id' => $bookingId, 'result' => $result]);
            return $result;
        } catch (GuzzleException $e) {
            Log::error('MPesa STK Push failed', ['error' => $e->getMessage(), 'booking_id' => $bookingId]);
            throw new \RuntimeException('MPesa STK Push failed: ' . $e->getMessage());
        }
    }

    /**
     * Query STK Push status.
     */
    public function stkQuery(string $checkoutRequestId): array
    {
        $token = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortCode . $this->passKey . $timestamp);

        $response = $this->client->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'BusinessShortCode' => $this->shortCode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Process STK Push callback from Safaricom.
     */
    public function processStkCallback(array $callbackData): void
    {
        $body = $callbackData['Body']['stkCallback'] ?? null;
        if (!$body) return;

        $checkoutId = $body['CheckoutRequestID'];
        $resultCode  = $body['ResultCode'];

        $payment = Payment::where('gateway_checkout_id', $checkoutId)->first();
        if (!$payment) {
            Log::warning('MPesa callback: payment not found', ['checkout_id' => $checkoutId]);
            return;
        }

        if ($resultCode === 0) {
            // Success — extract meta
            $meta = collect($body['CallbackMetadata']['Item'] ?? [])
                ->pluck('Value', 'Name');

            $payment->update([
                'status'            => 'completed',
                'gateway_reference' => $meta->get('MpesaReceiptNumber'),
                'gateway_response'  => $callbackData,
                'paid_at'           => now(),
            ]);

            // Trigger booking confirmation
            app(BookingService::class)->handlePaymentSuccess($payment);
        } else {
            $payment->update([
                'status'           => 'failed',
                'gateway_response' => $callbackData,
            ]);
        }
    }

    /**
     * B2C payout to owner/broker/driver wallet.
     */
    public function b2cPayout(string $phone, float $amount, string $remarks, string $occasion = ''): array
    {
        $token = $this->getAccessToken();
        $phone = $this->formatPhone($phone);

        $payload = [
            'InitiatorName'      => $this->b2cInitiatorName,
            'SecurityCredential' => $this->b2cSecurityCredential,
            'CommandID'          => 'BusinessPayment',
            'Amount'             => (int) floor($amount),
            'PartyA'             => $this->b2cShortCode,
            'PartyB'             => $phone,
            'Remarks'            => $remarks,
            'QueueTimeOutURL'    => route('mpesa.b2c.timeout'),
            'ResultURL'          => route('mpesa.b2c.result'),
            'Occasion'           => $occasion,
        ];

        try {
            $response = $this->client->post("{$this->baseUrl}/mpesa/b2c/v3/paymentrequest", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('MPesa B2C payout initiated', ['phone' => $phone, 'amount' => $amount, 'result' => $result]);
            return $result;
        } catch (GuzzleException $e) {
            Log::error('MPesa B2C payout failed', ['error' => $e->getMessage()]);
            throw new \RuntimeException('MPesa B2C payout failed: ' . $e->getMessage());
        }
    }

    /**
     * Process B2C result callback.
     */
    public function processB2cResult(array $result): void
    {
        $body = $result['Result'] ?? null;
        if (!$body) return;

        $resultCode        = $body['ResultCode'];
        $conversationId    = $body['ConversationID'];
        $originatorConvId  = $body['OriginatorConversationID'];

        Log::info('MPesa B2C result', [
            'result_code'      => $resultCode,
            'conversation_id'  => $conversationId,
            'originator_id'    => $originatorConvId,
        ]);

        // Find payment record by gateway_checkout_id = originatorConvId
        $payment = Payment::where('gateway_checkout_id', $originatorConvId)->first();
        if (!$payment) return;

        if ($resultCode === 0) {
            $params = collect($body['ResultParameters']['ResultParameter'] ?? [])
                ->pluck('Value', 'Key');
            $payment->update([
                'status'            => 'completed',
                'gateway_reference' => $params->get('TransactionReceipt'),
                'gateway_response'  => $result,
                'paid_at'           => now(),
            ]);
        } else {
            $payment->update(['status' => 'failed', 'gateway_response' => $result]);
        }
    }

    private function formatPhone(string $phone): string
    {
        // Ensure format: 2547XXXXXXXX
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+')) {
            $phone = ltrim($phone, '+');
        }
        return $phone;
    }
}
