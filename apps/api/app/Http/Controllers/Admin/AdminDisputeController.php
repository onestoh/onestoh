<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Services\EscrowService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminDisputeController extends Controller
{
    public function __construct(
        private EscrowService $escrowService,
        private NotificationService $notifications,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Dispute::with([
            'booking:id,asset_id,client_id,status,total_amount',
            'raisedBy:id,name,email,phone',
            'admin:id,name',
        ]);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('type', $request->type);

        return response()->json($query->latest()->paginate(20));
    }

    public function show(int $id): JsonResponse
    {
        $dispute = Dispute::with([
            'booking.asset',
            'booking.client',
            'booking.payments',
            'booking.escrow',
            'raisedBy',
            'admin',
        ])->findOrFail($id);

        return response()->json($dispute);
    }

    public function assign(Request $request, int $id): JsonResponse
    {
        $dispute = Dispute::findOrFail($id);
        $dispute->update(['status' => 'under_review', 'admin_id' => $request->user()->id]);

        return response()->json(['message' => 'Dispute assigned to you.']);
    }

    public function rule(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ruling'        => 'required|string|min:20|max:2000',
            'owner_amount'  => 'required|numeric|min:0',
            'client_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dispute = Dispute::with('booking')->findOrFail($id);

        if ($dispute->status === 'ruled') {
            return response()->json(['message' => 'Dispute already ruled.'], 409);
        }

        $split = ['owner' => $request->owner_amount, 'client' => $request->client_amount];

        $dispute->update([
            'status'       => 'ruled',
            'ruling'       => $request->ruling,
            'escrow_split' => $split,
            'resolved_at'  => now(),
        ]);

        // Release escrow with split
        $this->escrowService->releaseWithSplit($dispute->booking, $split, $request->user()->id);

        // Notify both parties
        $booking = $dispute->booking;
        $this->notifications->send($booking->client, 'dispute_resolved', [
            'booking_id' => $booking->id,
            'ruling'     => $request->ruling,
        ]);
        $this->notifications->send($booking->asset->owner, 'dispute_resolved', [
            'booking_id' => $booking->id,
            'ruling'     => $request->ruling,
        ]);

        return response()->json(['message' => 'Dispute ruled and escrow released.', 'dispute' => $dispute->fresh()]);
    }

    public function processAppeal(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:uphold,overturn',
            'notes'    => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dispute = Dispute::findOrFail($id);

        if ($dispute->status !== 'appealed') {
            return response()->json(['message' => 'Dispute is not in appeal state.'], 422);
        }

        $dispute->update([
            'status'      => 'closed',
            'ruling'      => $dispute->ruling . "\n\nAppeal ({$request->decision}): " . $request->notes,
            'resolved_at' => now(),
        ]);

        return response()->json(['message' => "Appeal {$request->decision}ed. Dispute closed."]);
    }
}
