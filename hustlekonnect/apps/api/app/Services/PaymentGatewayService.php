<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function __construct(
        private MpesaService $mpesa,
    ) {}

    public function initiatePayment(Booking $booking, string $method, string $phone = null): array
    {
        $idempotencyKey = hash('sha256', $booking->id . $method . $booking->total_amount_kes);

        $existing = Payment::where('idempotency_key', $idempotencyKey)->first();
        if ($existing && $existing->status === 'completed') {
            return ['status' => 'already_paid', 'payment_id' => $existing->id];
        }

        $payment = Payment::updateOrCreate(
            ['idempotency_key' => $idempotencyKey],
            [
                'id'             => (string) Str::uuid(),
                'booking_id'     => $booking->id,
                'user_id'        => $booking->user_id,
                'amount'         => $booking->total_amount_kes,
                'currency'       => $booking->currency ?? 'KES',
                'payment_method' => $method,
                'status'         => 'pending',
            ]
        );

        return match($method) {
            'mpesa'  => $this->initiateMpesa($payment, $booking, $phone),
            'wallet' => $this->initiateWallet($payment, $booking),
            default  => throw new \InvalidArgumentException("Unsupported payment method: {$method}"),
        };
    }

    private function initiateMpesa(Payment $payment, Booking $booking, string $phone): array
    {
        $ref  = 'HK-' . strtoupper(substr($booking->id, 0, 8));
        $desc = "HustleKonnect Booking {$ref}";

        $result = $this->mpesa->stkPush($phone, $payment->amount, $ref, $desc);

        if (isset($result['CheckoutRequestID'])) {
            $payment->update([
                'gateway_reference' => $result['CheckoutRequestID'],
                'status'            => 'processing',
                'gateway_response'  => $result,
            ]);
            return ['status' => 'processing', 'checkout_request_id' => $result['CheckoutRequestID'], 'payment_id' => $payment->id];
        }

        $payment->update(['status' => 'failed', 'gateway_response' => $result]);
        throw new \RuntimeException('M-Pesa STK Push failed: ' . ($result['errorMessage'] ?? 'Unknown error'));
    }

    private function initiateWallet(Payment $payment, Booking $booking): array
    {
        $wallet = $booking->user->getOrCreateWallet();
        if (!$wallet->hasSufficientBalance($payment->amount)) {
            throw new \RuntimeException('Insufficient wallet balance.');
        }

        $wallet->debit($payment->amount, 'booking_payment', $booking->id);
        $payment->update(['status' => 'completed', 'paid_at' => now()]);

        return ['status' => 'completed', 'payment_id' => $payment->id];
    }
}
