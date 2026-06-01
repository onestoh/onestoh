<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaystackService;
use Illuminate\Http\{JsonResponse, Request};

class PaystackController extends Controller
{
    public function __construct(private PaystackService $paystack) {}

    /**
     * Handle Paystack webhook events.
     * No auth — validated by HMAC signature.
     */
    public function webhook(Request $request): JsonResponse
    {
        $signature = $request->header('X-Paystack-Signature', '');
        $this->paystack->handleWebhook($request->all(), $signature);
        return response()->json(['status' => 'ok']);
    }
}
