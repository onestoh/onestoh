<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\EscrowAccount;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EscrowService
{
    /**
     * Create a new escrow record for a booking.
     */
    public function createEscrow(Booking $booking): EscrowAccount
    {
        return EscrowAccount::create([
            'booking_id'       => $booking->id,
            'rental_fee'       => $booking->base_amount,
            'security_deposit' => $booking->security_deposit_amount,
            'platform_fee'     => $booking->platform_fee,
            'broker_commission'=> $booking->broker_commission,
            'insurance_fee'    => $booking->insurance_fee,
            'total_collected'  => 0,
            'status'           => 'collecting',
        ]);
    }

    /**
     * Add collected funds to escrow.
     */
    public function addFunds(Booking $booking, float $amount, string $type): void
    {
        $escrow = $booking->escrow;
        if (!$escrow) return;

        $escrow->increment('total_collected', $amount);
    }

    /**
     * Mark escrow as fully held (all funds collected).
     */
    public function markHeld(Booking $booking): void
    {
        $booking->escrow?->update(['status' => 'held']);
    }

    /**
     * Freeze escrow due to dispute.
     */
    public function freezeForDispute(Booking $booking): void
    {
        $escrow = $booking->escrow;
        if ($escrow && $escrow->status === 'held') {
            $escrow->update(['status' => 'dispute_hold']);
            Log::info('Escrow frozen for dispute', ['booking_id' => $booking->id]);
        }
    }

    /**
     * Release escrow to owner and platform after booking completion.
     */
    public function release(Booking $booking, string $trigger = 'mutual_confirmation', ?int $adminId = null): void
    {
        DB::transaction(function () use ($booking, $trigger, $adminId) {
            $escrow = $booking->escrow;
            if (!$escrow || !in_array($escrow->status, ['held', 'dispute_hold'])) {
                throw new \DomainException('Escrow not in releasable state.');
            }

            $escrow->update(['status' => 'releasing']);

            $asset = $booking->asset;
            $owner = $asset->owner;

            // Credit owner with rental fee minus platform fee and broker commission
            $ownerAmount = $escrow->rental_fee - $escrow->platform_fee - $escrow->broker_commission;
            $ownerWallet = $owner->wallet ?? Wallet::create(['user_id' => $owner->id]);
            $ownerWallet->credit($ownerAmount, 'booking', $booking->id, "Rental earnings for booking #{$booking->id}");

            // Return security deposit to client if no damage dispute
            if ($escrow->security_deposit > 0 && $booking->status !== 'disputed') {
                $clientWallet = $booking->client->wallet ?? Wallet::create(['user_id' => $booking->client_id]);
                $clientWallet->credit($escrow->security_deposit, 'deposit_return', $booking->id, "Security deposit return for booking #{$booking->id}");
            }

            // Pay broker commission
            if ($escrow->broker_commission > 0 && $booking->broker_id) {
                $brokerWallet = $booking->broker->wallet ?? Wallet::create(['user_id' => $booking->broker_id]);
                $brokerWallet->credit($escrow->broker_commission, 'commission', $booking->id, "Broker commission for booking #{$booking->id}");
            }

            // Platform fee stays in platform (no wallet credit needed)

            $escrow->update([
                'status'          => 'released',
                'release_trigger' => $trigger,
                'released_by'     => $adminId,
                'released_at'     => now(),
            ]);

            $booking->update(['status' => 'closed']);

            Log::info('Escrow released', [
                'booking_id'   => $booking->id,
                'trigger'      => $trigger,
                'owner_amount' => $ownerAmount,
            ]);
        });
    }

    /**
     * Release escrow with dispute ruling split.
     */
    public function releaseWithSplit(Booking $booking, array $split, int $adminId): void
    {
        DB::transaction(function () use ($booking, $split, $adminId) {
            $escrow = $booking->escrow;
            if (!$escrow || $escrow->status !== 'dispute_hold') {
                throw new \DomainException('Escrow not in dispute_hold state.');
            }

            $ownerAmount  = $split['owner'] ?? 0;
            $clientAmount = $split['client'] ?? 0;

            if ($ownerAmount > 0) {
                $ownerWallet = $booking->asset->owner->wallet ?? Wallet::create(['user_id' => $booking->asset->owner_id]);
                $ownerWallet->credit($ownerAmount, 'booking', $booking->id, 'Dispute settlement - owner portion');
            }

            if ($clientAmount > 0) {
                $clientWallet = $booking->client->wallet ?? Wallet::create(['user_id' => $booking->client_id]);
                $clientWallet->credit($clientAmount, 'refund', $booking->id, 'Dispute settlement - client refund');
            }

            $escrow->update([
                'status'          => 'released',
                'release_trigger' => 'admin_override',
                'released_by'     => $adminId,
                'released_at'     => now(),
                'admin_notes'     => "Split: owner={$ownerAmount}, client={$clientAmount}",
            ]);

            $booking->update(['status' => 'closed']);
        });
    }

    /**
     * Refund escrow to client on cancellation.
     */
    public function refund(Booking $booking, string $cancelledBy): void
    {
        DB::transaction(function () use ($booking, $cancelledBy) {
            $escrow = $booking->escrow;
            if (!$escrow || $escrow->total_collected <= 0) return;

            $refundAmount = $escrow->total_collected;

            // Apply cancellation penalty if client cancelled after confirmation
            if ($cancelledBy === 'client' && in_array($booking->status, ['confirmed', 'owner_notified', 'client_prepared', 'active'])) {
                // Keep 10% cancellation fee for platform
                $penalty = $refundAmount * 0.10;
                $refundAmount -= $penalty;
            }

            if ($refundAmount > 0) {
                $clientWallet = $booking->client->wallet ?? Wallet::create(['user_id' => $booking->client_id]);
                $clientWallet->credit($refundAmount, 'refund', $booking->id, "Cancellation refund for booking #{$booking->id}");
            }

            $escrow->update(['status' => 'refunded']);

            // Release availability slots back to available
            $booking->availabilitySlots()->update(['status' => 'available', 'booking_id' => null]);

            Log::info('Escrow refunded', ['booking_id' => $booking->id, 'refund_amount' => $refundAmount]);
        });
    }
}
