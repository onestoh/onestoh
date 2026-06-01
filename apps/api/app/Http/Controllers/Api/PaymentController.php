<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private MpesaService $mpesa,
        private BookingService $bookingService,
    ) {}

    public function initiateMpesaStk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|uuid|exists:bookings,id',
            'phone'      => 'required|string|regex:/^(\+254|254|07|01)[0-9]{8}$/',
            'type'       => 'required|in:rental,deposit,insurance',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->client_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!in_array($booking->status, ['pending_payment', 'payment_processing'])) {
            return response()->json(['message' => 'Booking is not awaiting payment.'], 409);
        }

        $amount = match ($request->type) {
            'rental'    => $booking->base_amount + $booking->driver_surcharge + $booking->delivery_fee + $booking->insurance_fee + $booking->platform_fee,
            'deposit'   => $booking->security_deposit_amount,
            'insurance' => $booking->insurance_fee,
        };

        $idempotencyKey = hash('sha256', $booking->id . $request->type . $request->phone . $amount);

        // Prevent duplicate payments
        if (Payment::where('idempotency_key', $idempotencyKey)->where('status', 'completed')->exists()) {
            return response()->json(['message' => 'Payment already processed.'], 409);
        }

        $payment = Payment::firstOrCreate(
            ['idempotency_key' => $idempotencyKey],
            [
                'booking_id'    => $booking->id,
                'user_id'       => $request->user()->id,
                'gateway'       => 'mpesa_stk',
                'type'          => $request->type,
                'amount'        => $amount,
                'currency'      => 'KES',
                'status'        => 'pending',
                'phone_number'  => $request->phone,
            ]
        );

        try {
            $stkResponse = $this->mpesa->stkPush(
                $request->phone,
                $amount,
                $booking->id,
                'TOY-' . strtoupper(substr($booking->id, 0, 8)),
                'TheOnlineYard Rental Payment'
            );

            $payment->update([
                'status'              => 'processing',
                'gateway_checkout_id' => $stkResponse['CheckoutRequestID'] ?? null,
                'gateway_response'    => $stkResponse,
            ]);

            $this->bookingService->markPaymentProcessing($booking);

            return response()->json([
                'message'             => 'STK push sent. Please check your phone.',
                'payment_id'          => $payment->id,
                'checkout_request_id' => $stkResponse['CheckoutRequestID'] ?? null,
            ]);
        } catch (\Throwable $e) {
            $payment->update(['status' => 'failed']);
            Log::error('STK push error', ['error' => $e->getMessage(), 'booking_id' => $booking->id]);
            return response()->json(['message' => 'Payment initiation failed. Please try again.'], 500);
        }
    }

    public function mpesaStkCallback(Request $request): JsonResponse
    {
        Log::info('MPesa STK callback', ['payload' => $request->all()]);

        try {
            $this->mpesa->processStkCallback($request->all());
        } catch (\Throwable $e) {
            Log::error('MPesa callback processing error', ['error' => $e->getMessage()]);
        }

        // Always return 200 to Safaricom
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function mpesaB2cResult(Request $request): JsonResponse
    {
        Log::info('MPesa B2C result', ['payload' => $request->all()]);
        $this->mpesa->processB2cResult($request->all());
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function mpesaB2cTimeout(Request $request): JsonResponse
    {
        Log::warning('MPesa B2C timeout', ['payload' => $request->all()]);
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function queryPaymentStatus(Request $request, string $paymentId): JsonResponse
    {
        $payment = Payment::where('id', $paymentId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($payment->status === 'processing' && $payment->gateway_checkout_id) {
            try {
                $queryResult = $this->mpesa->stkQuery($payment->gateway_checkout_id);
                if (($queryResult['ResultCode'] ?? -1) === 0) {
                    $payment->update(['status' => 'completed', 'paid_at' => now()]);
                    $this->bookingService->handlePaymentSuccess($payment->fresh());
                } elseif (isset($queryResult['ResultCode']) && $queryResult['ResultCode'] !== 1032) {
                    $payment->update(['status' => 'failed']);
                }
            } catch (\Throwable $e) {
                Log::error('Payment query error', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'payment_id' => $payment->id,
            'status'     => $payment->fresh()->status,
            'paid_at'    => $payment->fresh()->paid_at,
        ]);
    }

    public function bookingPayments(Request $request, string $bookingId): JsonResponse
    {
        $booking = Booking::where('client_id', $request->user()->id)->findOrFail($bookingId);

        return response()->json($booking->payments()->get(['id', 'gateway', 'type', 'amount', 'status', 'paid_at', 'created_at']));
    }
}
