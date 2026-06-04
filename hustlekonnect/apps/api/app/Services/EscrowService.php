<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\EscrowAccount;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EscrowService
{
    public function release(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $escrow = EscrowAccount::where('booking_id', $booking->id)
                ->where('status', 'held')
                ->lockForUpdate()
                ->firstOrFail();

            $owner = $booking->asset?->yard?->user;
            if (!$owner) {
                Log::error("EscrowService: no owner found for booking {$booking->id}");
                return;
            }

            $wallet = $owner->getOrCreateWallet();
            $wallet->credit($escrow->owner_amount, 'escrow_release', $booking->id);

            $escrow->release();
            $booking->update(['status' => 'closed', 'completed_at' => $booking->completed_at ?? now()]);

            Log::info("Escrow released for booking {$booking->id}: {$escrow->owner_amount} {$escrow->currency}");
        });
    }

    public function refund(Booking $booking, float $amount = null): void
    {
        DB::transaction(function () use ($booking, $amount) {
            $escrow = EscrowAccount::where('booking_id', $booking->id)
                ->where('status', 'held')
                ->lockForUpdate()
                ->first();

            if (!$escrow) return;

            $refundAmount = $amount ?? $escrow->total_amount;
            $client = $booking->user;
            if ($client) {
                $wallet = $client->getOrCreateWallet();
                $wallet->credit($refundAmount, 'refund', $booking->id);
            }

            $escrow->update(['status' => 'refunded', 'released_at' => now()]);
            Log::info("Escrow refunded for booking {$booking->id}: {$refundAmount}");
        });
    }
}
