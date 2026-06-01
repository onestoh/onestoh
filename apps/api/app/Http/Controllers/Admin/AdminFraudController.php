<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudFlag;
use App\Models\FraudRiskProfile;
use App\Models\User;
use App\Services\FraudDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminFraudController extends Controller
{
    public function __construct(private FraudDetectionService $service) {}

    public function index(Request $request): JsonResponse
    {
        $query = FraudFlag::with(['user:id,name,email', 'reviewer:id,name'])
            ->orderByDesc('risk_score');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('flag_type')) {
            $query->where('flag_type', $request->flag_type);
        }
        if ($request->has('min_risk')) {
            $query->where('risk_score', '>=', $request->min_risk);
        }

        return response()->json(['data' => $query->paginate(20)]);
    }

    public function show(FraudFlag $flag): JsonResponse
    {
        $flag->load(['user', 'reviewer']);
        return response()->json(['data' => $flag]);
    }

    public function resolve(Request $request, FraudFlag $flag): JsonResponse
    {
        $validated = $request->validate([
            'status'           => 'required|in:confirmed_fraud,false_positive,resolved',
            'resolution_notes' => 'required|string|max:1000',
        ]);

        $flag->update([
            'status'           => $validated['status'],
            'resolution_notes' => $validated['resolution_notes'],
            'reviewed_by'      => $request->user()->id,
            'resolved_at'      => now(),
        ]);

        // Recalculate risk profile after resolution
        if ($flag->user_id) {
            $this->service->updateRiskProfile(User::find($flag->user_id));
        }

        return response()->json(['message' => 'Fraud flag resolved', 'data' => $flag->fresh()]);
    }

    public function riskProfiles(Request $request): JsonResponse
    {
        $profiles = FraudRiskProfile::with('user:id,name,email,phone')
            ->where('overall_risk_score', '>=', $request->input('min_score', 40))
            ->orderByDesc('overall_risk_score')
            ->paginate(20);

        return response()->json(['data' => $profiles]);
    }

    public function bulkSuspend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'reason'     => 'required|string|max:500',
        ]);

        $count = User::whereIn('id', $validated['user_ids'])
            ->update([
                'suspended_at'     => now(),
                'suspension_reason'=> $validated['reason'],
            ]);

        return response()->json([
            'message'        => "{$count} account(s) suspended.",
            'suspended_count'=> $count,
        ]);
    }
}
