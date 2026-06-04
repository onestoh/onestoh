<?php
namespace App\Observers;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\EscrowAccount;

class PaymentObserver
{
    public function updated(Payment $payment): void
    {
        if (!$payment->isDirty('status') || $payment->status !== 'completed') return;

        $booking = Booking::find($payment->booking_id);
        if (!$booking) return;

        if (in_array($booking->status, ['pending_payment', 'payment_processing'])) {
            $booking->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        }

        EscrowAccount::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'total_amount' => $payment->amount,
                'platform_fee' => $payment->amount * 0.05,
                'owner_amount' => $payment->amount * 0.95,
                'status'       => 'held',
                'held_at'      => now(),
            ]
        );
    }
}
