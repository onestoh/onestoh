<?php

namespace App\Http\Controllers;

use App\Models\PayoutRequest;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

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
}
