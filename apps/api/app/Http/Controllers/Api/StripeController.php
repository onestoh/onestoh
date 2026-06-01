<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function __construct(private StripeService $service) {}

    public function createIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'currency'   => 'nullable|string|size:3|in:USD,GBP,EUR,KES,UGX,TZS',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);

        if ($booking->renter_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        try {
            $intent = $this->service->createPaymentIntent($booking, $validated['currency'] ?? 'usd');
            return response()->json(['data' => [
                'payment_intent_id'    => $intent['id'],
                'client_secret'        => $intent['client_secret'],
                'amount'               => $intent['amount'],
                'currency'             => $intent['currency'],
                'status'               => $intent['status'],
            ]]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function refund(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $payment = Payment::findOrFail($validated['payment_id']);
        $booking = $payment->booking;

        if ($booking->renter_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $success = $this->service->createRefund($payment, $validated['amount']);

        return $success
            ? response()->json(['message' => 'Refund initiated successfully'])
            : response()->json(['error' => 'Refund failed. Please contact support.'], 422);
    }

    public function webhook(Request $request): JsonResponse
    {
        $signature = $request->header('Stripe-Signature', '');
        $payload   = $request->all();

        Log::info('Stripe webhook received', ['type' => $payload['type'] ?? 'unknown']);

        try {
            $this->service->handleWebhook($payload, $signature);
        } catch (\RuntimeException $e) {
            Log::error('Stripe webhook error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['status' => 'ok']);
    }
}
