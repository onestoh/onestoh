<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::withTrashed()->with('wallet:id,user_id,balance');

        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('kyc_status')) $query->where('kyc_status', $request->kyc_status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"));
        }

        return response()->json($query->latest()->paginate(25));
    }

    public function show(int $id): JsonResponse
    {
        $user = User::withTrashed()->with([
            'wallet',
            'kycDocuments:id,user_id,document_type,status,reviewed_at',
            'assets:id,owner_id,category,make,model,status,is_published',
            'driver',
        ])->findOrFail($id);

        return response()->json($user);
    }

    public function suspend(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), ['reason' => 'required|string|max:500']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return response()->json(['message' => 'Cannot suspend super admin.'], 403);
        }

        $user->update(['kyc_status' => 'suspended']);
        $user->delete(); // soft delete

        return response()->json(['message' => 'User suspended.']);
    }

    public function restore(int $id): JsonResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $user->update(['kyc_status' => 'approved']);

        return response()->json(['message' => 'User restored.']);
    }

    public function updateRole(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:yard_owner,individual_owner,client,broker,driver',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        User::findOrFail($id)->update(['role' => $request->role]);

        return response()->json(['message' => 'Role updated.']);
    }

    public function adjustTrustScore(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'score'  => 'required|integer|min:0|max:100',
            'reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        User::findOrFail($id)->update(['trust_score' => $request->score]);

        return response()->json(['message' => "Trust score updated to {$request->score}."]);
    }
}
