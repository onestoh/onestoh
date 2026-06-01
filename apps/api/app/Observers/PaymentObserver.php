<?php
namespace App\Observers;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\EscrowAccount;
use App\Jobs\SendBookingNotificationsJob;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    public function updated(Payment $payment): void
    {
        if (!$payment->isDirty('status')) {
            return;
        }

        if ($payment->status !== 'completed') {
            return;
        }

        $booking = Booking::with(['user', 'asset.yard.user'])->find($payment->booking_id);

        if (!$booking) {
            return;
        }

        // Payment confirmed -> advance booking to confirmed
        if ($booking->status === 'pending_payment' || $booking->status === 'payment_processing') {
            $booking->update(['status' => 'confirmed', 'confirmed_at' => now()]);
            Log::info("PaymentObserver: booking {$booking->id} confirmed after payment {$payment->id}");
        }

        // Create or update escrow account
        EscrowAccount::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'total_amount' => $payment->amount,
                'platform_fee' => $payment->amount * 0.05,
                'owner_amount' => $payment->amount * 0.95,
                'status' => 'held',
                'held_at' => now(),
            ]
        );
    }
}
