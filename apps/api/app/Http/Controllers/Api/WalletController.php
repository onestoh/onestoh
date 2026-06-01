<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function __construct(private MpesaService $mpesa) {}

    public function balance(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet ?? Wallet::create(['user_id' => $request->user()->id]);

        return response()->json([
            'balance'         => $wallet->balance,
            'pending_balance' => $wallet->pending_balance,
            'deposit_balance' => $wallet->deposit_balance,
            'currency'        => $wallet->currency,
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;
        if (!$wallet) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $query = $wallet->transactions();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }

        return response()->json($query->paginate(20));
    }

    public function requestPayout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100|max:999999',
            'phone'  => 'required|string|regex:/^(\+254|254|07|01)[0-9]{8}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user   = $request->user();
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient wallet balance.'], 400);
        }

        // Debit wallet immediately
        $wallet->debit(
            $request->amount,
            'payout',
            Str::uuid(),
            'Payout to M-Pesa ' . $request->phone
        );

        // Initiate B2C payout
        try {
            $b2cResponse = $this->mpesa->b2cPayout(
                $request->phone,
                $request->amount,
                "TheOnlineYard payout to {$user->name}",
                'Wallet payout'
            );

            return response()->json([
                'message'          => 'Payout initiated. Funds will arrive shortly.',
                'conversation_id'  => $b2cResponse['ConversationID'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // Re-credit on failure
            $wallet->credit($request->amount, 'adjustment', 'payout_failed', 'Payout failed - reversal');
            return response()->json(['message' => 'Payout failed. Please try again.'], 500);
        }
    }
}
