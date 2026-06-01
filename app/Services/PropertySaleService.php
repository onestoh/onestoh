<?php
namespace App\Services;

use App\Models\EscrowTransaction;
use App\Models\Property;
use App\Models\User;

class PropertySaleService
{
    public static function initiateEscrow(int $propertyId, int $buyerId, float $amount): EscrowTransaction
    {
        $property = Property::findOrFail($propertyId);
        $buyer    = User::findOrFail($buyerId);
        $ref      = 'ESCROW-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $escrow = EscrowTransaction::create([
            'property_id' => $propertyId,
            'buyer_id'    => $buyerId,
            'seller_id'   => $property->user_id,
            'amount'      => $amount,
            'type'        => 'sale',
            'status'      => 'held',
            'reference'   => $ref,
            'notes'       => "Sale initiated by {$buyer->name}",
        ]);

        // Stage 1: Notify seller — sale initiated
        NotificationService::send($property->user_id, 'Sale Offer Received!',
            "{$buyer->name} has initiated a purchase of {$property->title} for KES " . number_format($amount) . ". Funds held in escrow (Ref: {$ref}). Awaiting verification.",
            'payment', '/dashboard/escrow');

        // Stage 2: Notify buyer — escrow created
        NotificationService::send($buyerId, 'Escrow Created',
            "Your payment of KES " . number_format($amount) . " for {$property->title} is held in escrow (Ref: {$ref}). The seller has been notified.",
            'payment', '/dashboard/escrow');

        // Stage 3: Notify admins
        $admins = User::where('role', 'admin')->pluck('id');
        foreach ($admins as $adminId) {
            NotificationService::send($adminId, 'New Escrow Transaction',
                "Escrow {$ref}: {$buyer->name} → {$property->title} · KES " . number_format($amount) . ". Requires document verification.",
                'system', '/admin/escrow');
        }

        return $escrow;
    }

    public static function releaseEscrow(EscrowTransaction $escrow): EscrowTransaction
    {
        $escrow->update(['status' => 'released', 'released_at' => now()]);

        $property = Property::find($escrow->property_id);
        if ($property) {
            $property->update(['status' => 'sold']);
        }

        NotificationService::send($escrow->seller_id, 'Payment Released!',
            "Escrow funds of KES " . number_format($escrow->amount) . " for " . optional($property)->title . " have been released to your account. Transfer within 2-3 business days.",
            'payment', '/dashboard/escrow');

        NotificationService::send($escrow->buyer_id, 'Property Transfer Initiated',
            "Congratulations! Payment confirmed and property transfer for " . optional($property)->title . " is in progress. You will receive title deed documents within 30 days.",
            'payment', '/dashboard/escrow');

        return $escrow->fresh();
    }
}
