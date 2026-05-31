<?php

namespace App\Http\Controllers;

use App\Models\EscrowTransaction;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class EscrowController extends Controller
{
    public function initiate(Request $request)
    {
        $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'buyer_id'    => 'required|integer|exists:users,id',
            'amount'      => 'required|numeric|min:1',
            'type'        => 'sometimes|in:sale,deposit,refund',
        ]);

        $userId = session('user_id');
        $ref    = 'ESCROW-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        $escrow = EscrowTransaction::create([
            'property_id' => $request->property_id,
            'buyer_id'    => $request->buyer_id,
            'seller_id'   => $userId,
            'amount'      => $request->amount,
            'type'        => $request->type ?? 'sale',
            'status'      => 'held',
            'reference'   => $ref,
            'notes'       => $request->notes,
        ]);

        try {
            NotificationService::send(
                $request->buyer_id,
                'Escrow Initiated',
                'An escrow of KES ' . number_format($request->amount, 0) . ' has been set up. Reference: ' . $ref,
                'payment',
                '/dashboard'
            );
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'data'    => [
                'escrow_id'    => $escrow->id,
                'reference'    => $ref,
                'amount'       => 'KES ' . number_format($request->amount, 2),
                'status'       => 'held',
                'instructions' => 'Funds are held in escrow. They will be released upon confirmation of property transfer.',
            ],
        ], 201);
    }

    public function release($id)
    {
        $escrow = EscrowTransaction::findOrFail($id);
        $userId = session('user_id');
        $role   = session('role');

        // Only seller or admin can release
        if ($role !== 'admin' && $escrow->seller_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($escrow->status !== 'held') {
            return response()->json(['success' => false, 'message' => 'Escrow is not in held status.'], 422);
        }

        $escrow->update(['status' => 'released', 'released_at' => now()]);

        try {
            NotificationService::send($escrow->buyer_id, 'Escrow Released', 'Escrow funds have been released. Reference: ' . $escrow->reference, 'payment');
            NotificationService::send($escrow->seller_id, 'Escrow Released', 'You have released escrow funds. Reference: ' . $escrow->reference, 'payment');
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'data'    => ['status' => 'released', 'released_at' => $escrow->released_at],
        ]);
    }

    public function dispute($id)
    {
        $escrow = EscrowTransaction::findOrFail($id);
        $userId = session('user_id');

        // Buyer or seller can dispute
        if ($escrow->buyer_id !== $userId && $escrow->seller_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $escrow->update(['status' => 'disputed']);

        // Notify admin(s)
        try {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                NotificationService::send(
                    $admin->id,
                    'Escrow Dispute',
                    'Escrow ' . $escrow->reference . ' has been disputed.',
                    'system',
                    '/admin/escrow'
                );
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'data'    => ['status' => 'disputed', 'reference' => $escrow->reference],
        ]);
    }
}
