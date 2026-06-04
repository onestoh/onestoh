<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    public function getAccessToken(): string
    {
        return Cache::remember('mpesa_token', 3500, function () {
            $response = Http::withBasicAuth(
                config('services.mpesa.consumer_key'),
                config('services.mpesa.consumer_secret')
            )->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            return $response->json('access_token');
        });
    }

    public function stkPush(string $phone, float $amount, string $accountRef, string $description): array
    {
        $token     = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode(
            config('services.mpesa.shortcode') . config('services.mpesa.passkey') . $timestamp
        );

        $phone = preg_replace('/^0/', '254', $phone);
        $phone = preg_replace('/^\+/', '', $phone);

        $response = Http::withToken($token)->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
            'BusinessShortCode' => config('services.mpesa.shortcode'),
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => (int) ceil($amount),
            'PartyA'            => $phone,
            'PartyB'            => config('services.mpesa.shortcode'),
            'PhoneNumber'       => $phone,
            'CallBackURL'       => config('services.mpesa.callback_url'),
            'AccountReference'  => $accountRef,
            'TransactionDesc'   => $description,
        ]);

        Log::info('M-Pesa STK Push', ['phone' => $phone, 'amount' => $amount, 'response' => $response->json()]);
        return $response->json();
    }

    public function queryStatus(string $checkoutRequestId): array
    {
        $token     = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode(
            config('services.mpesa.shortcode') . config('services.mpesa.passkey') . $timestamp
        );

        $response = Http::withToken($token)->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", [
            'BusinessShortCode' => config('services.mpesa.shortcode'),
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ]);

        return $response->json();
    }
}
