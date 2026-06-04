<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function balance(Request $request): JsonResponse
    {
        $wallet = $request->user()->getOrCreateWallet();
        return response()->json([
            'data' => [
                'balance'  => $wallet->balance,
                'currency' => $wallet->currency,
                'is_frozen'=> $wallet->is_frozen,
            ]
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $wallet = $request->user()->getOrCreateWallet();
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->latest()
            ->paginate(20);

        return response()->json($transactions);
    }
}
