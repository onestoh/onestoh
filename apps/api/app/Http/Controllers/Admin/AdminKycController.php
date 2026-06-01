<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminKycController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function queue(Request $request): JsonResponse
    {
        $query = User::with(['kycDocuments' => fn($q) => $q->select(['id', 'user_id', 'document_type', 'status', 'created_at'])])
            ->where('kyc_status', 'pending')
            ->whereNotNull('kyc_submitted_at');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        return response()->json($query->oldest('kyc_submitted_at')->paginate(20));
    }

    public function userDocuments(int $userId): JsonResponse
    {
        $user = User::with('kycDocuments')->findOrFail($userId);
        $docs = $user->kycDocuments->map(function ($doc) {
            return array_merge($doc->toArray(), [
                'signed_url' => Storage::disk('s3')->temporaryUrl($doc->file_path, now()->addMinutes(30)),
            ]);
        });

        return response()->json([
            'user'      => $user->only(['id', 'name', 'email', 'phone', 'role', 'kyc_status', 'kyc_submitted_at']),
            'documents' => $docs,
        ]);
    }

    public function approveDocument(Request $request, int $docId): JsonResponse
    {
        $doc = KycDocument::with('user')->findOrFail($docId);
        $doc->update(['status' => 'approved', 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        // Check if all required docs approved for auto-approving user KYC
        $this->checkAndUpdateUserKyc($doc->user, $request->user()->id);

        return response()->json(['message' => 'Document approved.']);
    }

    public function rejectDocument(Request $request, int $docId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doc = KycDocument::with('user')->findOrFail($docId);
        $doc->update([
            'status'           => 'rejected',
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
            'rejection_reason' => $request->reason,
        ]);

        // Notify user
        $this->notifications->send($doc->user, 'kyc_rejected', ['reason' => $request->reason]);

        return response()->json(['message' => 'Document rejected.']);
    }

    public function approveUserKyc(Request $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $user->update([
            'kyc_status'       => 'approved',
            'kyc_reviewed_at'  => now(),
            'kyc_reviewed_by'  => $request->user()->id,
        ]);

        $this->notifications->send($user, 'kyc_approved', []);

        return response()->json(['message' => 'User KYC approved.']);
    }

    public function rejectUserKyc(Request $request, int $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($userId);
        $user->update([
            'kyc_status'             => 'rejected',
            'kyc_reviewed_at'        => now(),
            'kyc_reviewed_by'        => $request->user()->id,
            'kyc_rejection_reason'   => $request->reason,
        ]);

        $this->notifications->send($user, 'kyc_rejected', ['reason' => $request->reason]);

        return response()->json(['message' => 'User KYC rejected.']);
    }

    private function checkAndUpdateUserKyc(User $user, int $adminId): void
    {
        $required = match ($user->role) {
            'client'           => ['national_id_front', 'national_id_back', 'selfie_with_id'],
            'individual_owner' => ['national_id_front', 'national_id_back', 'selfie_with_id', 'kra_pin_certificate'],
            'yard_owner'       => ['national_id_front', 'national_id_back', 'selfie_with_id', 'business_certificate', 'kra_pin_certificate'],
            'broker'           => ['national_id_front', 'national_id_back', 'selfie_with_id', 'broker_registration'],
            'driver'           => ['national_id_front', 'national_id_back', 'driving_licence', 'police_clearance'],
            default            => ['national_id_front', 'national_id_back', 'selfie_with_id'],
        };

        $approved = KycDocument::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereIn('document_type', $required)
            ->pluck('document_type')
            ->toArray();

        if (count(array_diff($required, $approved)) === 0) {
            $user->update([
                'kyc_status'      => 'approved',
                'kyc_reviewed_at' => now(),
                'kyc_reviewed_by' => $adminId,
            ]);
            app(NotificationService::class)->send($user, 'kyc_approved', []);
        }
    }
}
