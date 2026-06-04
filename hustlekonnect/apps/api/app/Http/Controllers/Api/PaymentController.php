<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentGatewayService;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentGatewayService $gatewayService,
        private MpesaService $mpesaService,
    ) {}

    public function initiate(Request $request, string $bookingId): JsonResponse
    {
        $request->validate([
            'method' => 'required|in:mpesa,wallet,flutterwave,paystack,stripe,mtn_momo',
            'phone'  => 'nullable|string|max:20',
        ]);

        $booking = Booking::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending_payment', 'payment_failed'])
            ->findOrFail($bookingId);

        try {
            $result = $this->gatewayService->initiatePayment(
                $booking,
                $request->method,
                $request->phone
            );
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Payment initiation failed', ['booking' => $bookingId, 'error' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function mpesaCallback(Request $request): JsonResponse
    {
        $data = $request->all();
        Log::info('M-Pesa Callback', $data);

        $body = $data['Body']['stkCallback'] ?? null;
        if (!$body) return response()->json(['ResultCode' => 0]);

        $checkoutId = $body['CheckoutRequestID'] ?? null;
        $resultCode = $body['ResultCode'] ?? 1;

        $payment = Payment::where('gateway_reference', $checkoutId)->first();
        if (!$payment) return response()->json(['ResultCode' => 0]);

        if ($resultCode == 0) {
            $metadata = collect($body['CallbackMetadata']['Item'] ?? [])
                ->keyBy('Name')->map->Value->toArray();

            $payment->update([
                'status'             => 'completed',
                'paid_at'            => now(),
                'gateway_response'   => $data,
                'gateway_transaction_id' => $metadata['MpesaReceiptNumber'] ?? null,
            ]);
        } else {
            $payment->update(['status' => 'failed', 'gateway_response' => $data]);
        }

        return response()->json(['ResultCode' => 0]);
    }

    public function history(Request $request): JsonResponse
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->with('booking:id,start_date,end_date')
            ->latest()
            ->paginate(20);

        return response()->json($payments);
    }
}
