<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\KycDocument;
use App\Models\Yard;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'data' => [
                'users'          => User::count(),
                'active_users'   => User::where('is_active', true)->count(),
                'kyc_pending'    => KycDocument::where('status', 'pending')->count(),
                'yards'          => Yard::count(),
                'assets'         => Asset::where('is_listed', true)->count(),
                'bookings_total' => Booking::count(),
                'bookings_active'=> Booking::where('status', 'active')->count(),
                'revenue_kes'    => Payment::where('status', 'completed')->sum('amount'),
                'pending_jobs'   => DB::table('jobs')->count(),
                'failed_jobs'    => DB::table('failed_jobs')->count(),
            ]
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->kyc_status, fn($q) => $q->where('kyc_status', $request->kyc_status))
            ->when($request->search, fn($q) =>
                $q->where(fn($inner) =>
                    $inner->where('name', 'like', "%{$request->search}%")
                          ->orWhere('email', 'like', "%{$request->search}%")
                          ->orWhere('phone', 'like', "%{$request->search}%")
                )
            )
            ->latest()
            ->paginate(25);

        return response()->json($users);
    }

    public function toggleUser(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return response()->json(['message' => "User {$status} successfully."]);
    }

    public function kycQueue(Request $request): JsonResponse
    {
        $docs = KycDocument::with('user:id,name,email,phone,country')
            ->where('status', 'pending')
            ->oldest()
            ->paginate(20);

        return response()->json($docs);
    }

    public function reviewKyc(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'action'           => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        $doc = KycDocument::findOrFail($id);

        if ($request->action === 'approve') {
            $doc->update(['status' => 'approved', 'verified_at' => now(), 'verified_by' => $request->user()->id]);
            $doc->user->update(['kyc_status' => 'approved']);
        } else {
            $doc->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
            $doc->user->update(['kyc_status' => 'rejected']);
        }

        return response()->json(['message' => "KYC {$request->action}d successfully."]);
    }

    public function bookings(Request $request): JsonResponse
    {
        $bookings = Booking::with(['user:id,name,email', 'asset:id,title'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(25);

        return response()->json($bookings);
    }

    public function disputes(Request $request): JsonResponse
    {
        $disputes = DB::table('disputes')
            ->join('bookings', 'disputes.booking_id', '=', 'bookings.id')
            ->join('users', 'disputes.raised_by', '=', 'users.id')
            ->select('disputes.*', 'users.name as raised_by_name', 'bookings.status as booking_status')
            ->where('disputes.status', 'open')
            ->latest('disputes.created_at')
            ->paginate(20);

        return response()->json($disputes);
    }

    public function revenueReport(Request $request): JsonResponse
    {
        $request->validate(['from' => 'nullable|date', 'to' => 'nullable|date']);

        $from = $request->get('from', now()->startOfMonth());
        $to   = $request->get('to', now()->endOfMonth());

        $data = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$from, $to])
            ->selectRaw("DATE(paid_at) as date, COUNT(*) as count, SUM(amount) as total, currency")
            ->groupBy(DB::raw('DATE(paid_at)'), 'currency')
            ->orderBy('date')
            ->get();

        return response()->json(['data' => $data, 'from' => $from, 'to' => $to]);
    }
}
