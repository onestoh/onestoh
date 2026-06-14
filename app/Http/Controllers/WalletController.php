<?php

namespace App\Http\Controllers;

use App\Models\PayoutRequest;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('wallet');
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(20);
        $payouts = PayoutRequest::where('user_id', $user->id)->latest()->limit(5)->get();

        return view('wallet.index', compact('user', 'transactions', 'payouts'));
    }

    public function requestPayout(Request $request)
    {
        $user = auth()->user()->load('wallet');
        $min = \App\Models\PlatformSetting::get('min_payout_amount', 500);

        $data = $request->validate([
            'amount'          => ['required', 'numeric', 'min:' . $min, 'max:' . ($user->wallet?->balance ?? 0)],
            'mpesa_number'    => ['required', 'string', 'regex:/^(?:254|\+254|0)?(7[0-9]{8})$/'],
            'account_name'    => ['required', 'string', 'max:100'],
        ]);

        if (($user->wallet?->balance ?? 0) < $data['amount']) {
            return back()->withErrors(['amount' => 'Insufficient wallet balance.']);
        }

        // Debit wallet (holds the amount pending payout)
        $user->wallet->debit($data['amount'], 'payout_hold', 'Payout request hold', null);

        PayoutRequest::create([
            'user_id'      => $user->id,
            'amount'       => $data['amount'],
            'mpesa_number' => $data['mpesa_number'],
            'account_name' => $data['account_name'],
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Payout request submitted. Processing within 24 hours.');
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:100|max:100000',
            'phone'  => 'required|string',
        ]);

        $phone  = preg_replace('/^0/', '254', preg_replace('/\D/', '', $request->phone));
        $wallet = auth()->user()->wallet;
        $ref    = 'WTOP-' . strtoupper(Str::random(8));

        $wallet->transactions()->create([
            'type'        => 'topup_pending',
            'amount'      => $request->amount,
            'description' => 'Wallet top-up via M-Pesa',
            'reference'   => $ref,
        ]);

        try {
            $this->initiateMpesaStk($phone, (int) $request->amount, $ref);
            return back()->with('success', 'M-Pesa prompt sent to ' . $request->phone . '. Enter your PIN to complete.');
        } catch (\Exception $e) {
            return back()->with('error', 'Could not initiate M-Pesa payment. Try again.');
        }
    }

    public function topupCallback(Request $request)
    {
        $data   = $request->all();
        $result = $data['Body']['stkCallback'] ?? null;
        if (!$result) return response()->json(['ok' => true]);

        $ref  = $result['MerchantRequestID'] ?? null;
        $code = $result['ResultCode'] ?? -1;

        $tx = WalletTransaction::where('reference', $ref)->first();
        if ($tx && $code == 0) {
            $wallet = $tx->wallet;
            $wallet->increment('balance', $tx->amount);
            $wallet->transactions()->create([
                'type'          => 'topup',
                'amount'        => $tx->amount,
                'balance_after' => $wallet->fresh()->balance,
                'description'   => 'Wallet top-up via M-Pesa',
                'reference'     => $ref . '-CONFIRMED',
            ]);
            $tx->delete();
        }

        return response()->json(['ok' => true]);
    }

    private function initiateMpesaStk(string $phone, int $amount, string $reference): array
    {
        $consumerKey    = config('services.mpesa.consumer_key');
        $consumerSecret = config('services.mpesa.consumer_secret');
        $shortcode      = config('services.mpesa.shortcode');
        $passkey        = config('services.mpesa.passkey');
        $baseUrl        = config('services.mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';

        $token = Http::withBasicAuth($consumerKey, $consumerSecret)
            ->get("$baseUrl/oauth/v1/generate?grant_type=client_credentials")
            ->json('access_token');

        $timestamp   = now()->format('YmdHis');
        $password    = base64_encode($shortcode . $passkey . $timestamp);
        $callbackUrl = route('wallet.topup.callback');

        return Http::withToken($token)->post("$baseUrl/mpesa/stkpush/v1/processrequest", [
            'BusinessShortCode' => $shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => $amount,
            'PartyA'            => $phone,
            'PartyB'            => $shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $callbackUrl,
            'AccountReference'  => $reference,
            'TransactionDesc'   => 'Wallet Top-up',
        ])->json();
    }
}
