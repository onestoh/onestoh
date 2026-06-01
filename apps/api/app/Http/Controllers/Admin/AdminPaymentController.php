<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\EscrowAccount;
use App\Models\Payment;
use App\Services\EscrowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminPaymentController extends Controller
{
    public function __construct(private EscrowService $escrowService) {}

    public function payments(Request $request): JsonResponse
    {
        $query = Payment::with(['user:id,name,email', 'booking:id,asset_id,status'])->latest();

        if ($request->filled('gateway')) $query->where('gateway', $request->gateway);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->to);

        return response()->json($query->paginate(25));
    }

    public function escrowAccounts(Request $request): JsonResponse
    {
        $query = EscrowAccount::with(['booking:id,asset_id,client_id,status,start_at,end_at'])->latest();

        if ($request->filled('status')) $query->where('status', $request->status);

        return response()->json($query->paginate(25));
    }

    public function releaseEscrow(Request $request, string $bookingId): JsonResponse
    {
        $booking = Booking::with('escrow')->findOrFail($bookingId);

        if (!$booking->escrow || !in_array($booking->escrow->status, ['held', 'dispute_hold'])) {
            return response()->json(['message' => 'Escrow is not in a releasable state.'], 422);
        }

        $this->escrowService->release($booking, 'admin_override', $request->user()->id);

        return response()->json(['message' => 'Escrow released successfully.']);
    }

    public function escrowSplit(Request $request, string $bookingId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'owner_amount'  => 'required|numeric|min:0',
            'client_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::with('escrow')->findOrFail($bookingId);

        $totalSplit = $request->owner_amount + $request->client_amount;
        if ($totalSplit > $booking->escrow->total_collected) {
            return response()->json(['message' => 'Split amount exceeds total collected.'], 422);
        }

        $this->escrowService->releaseWithSplit(
            $booking,
            ['owner' => $request->owner_amount, 'client' => $request->client_amount],
            $request->user()->id
        );

        return response()->json(['message' => 'Escrow split and released.']);
    }

    public function paymentStats(Request $request): JsonResponse
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to ?? now()->toDateString();

        $stats = [
            'total_collected'  => Payment::where('status', 'completed')->whereBetween('created_at', [$from, $to])->sum('amount'),
            'platform_fees'    => Booking::whereBetween('created_at', [$from, $to])->whereIn('status', ['completed', 'closed'])->sum('platform_fee'),
            'broker_commissions' => Booking::whereBetween('created_at', [$from, $to])->sum('broker_commission'),
            'total_refunds'    => Payment::where('type', 'refund')->where('status', 'completed')->whereBetween('created_at', [$from, $to])->sum('amount'),
            'by_gateway'       => Payment::where('status', 'completed')
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('gateway')
                ->selectRaw('gateway, count(*) as count, sum(amount) as total')
                ->get(),
        ];

        return response()->json(array_merge($stats, ['period' => compact('from', 'to')]));
    }
}
