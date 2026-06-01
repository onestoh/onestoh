<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $docs = KycDocument::where('user_id', $user->id)
            ->select(['id', 'document_type', 'status', 'rejection_reason', 'created_at'])
            ->get();

        return response()->json([
            'kyc_status'      => $user->kyc_status,
            'kyc_submitted_at'=> $user->kyc_submitted_at,
            'documents'       => $docs,
            'required_documents' => $this->getRequiredDocuments($user->role),
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:national_id_front,national_id_back,selfie_with_id,driving_licence,business_certificate,kra_pin_certificate,ntsa_certificate,police_clearance,operator_certificate,proof_of_address,broker_registration,company_bank_details',
            'file'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $file = $request->file('file');

        // Upload to S3
        $path = Storage::disk('s3')->putFileAs(
            "kyc/{$user->id}",
            $file,
            $request->document_type . '_' . time() . '.' . $file->extension(),
            'private'
        );

        // Replace existing pending doc of same type
        KycDocument::where('user_id', $user->id)
            ->where('document_type', $request->document_type)
            ->where('status', 'pending')
            ->delete();

        $doc = KycDocument::create([
            'user_id'       => $user->id,
            'document_type' => $request->document_type,
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'status'        => 'pending',
        ]);

        // Check if all required docs uploaded and update kyc_submitted_at
        $required = $this->getRequiredDocuments($user->role);
        $uploaded = KycDocument::where('user_id', $user->id)
            ->whereIn('document_type', $required)
            ->pluck('document_type')
            ->toArray();

        if (count(array_diff($required, $uploaded)) === 0 && !$user->kyc_submitted_at) {
            $user->update(['kyc_submitted_at' => now()]);
        }

        return response()->json([
            'message'  => 'Document uploaded successfully.',
            'document' => [
                'id'            => $doc->id,
                'document_type' => $doc->document_type,
                'status'        => $doc->status,
                'created_at'    => $doc->created_at,
            ],
        ], 201);
    }

    public function getDocumentUrl(Request $request, int $id): JsonResponse
    {
        $doc = KycDocument::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Generate signed URL (15 min)
        $url = Storage::disk('s3')->temporaryUrl($doc->file_path, now()->addMinutes(15));

        return response()->json(['url' => $url]);
    }

    private function getRequiredDocuments(string $role): array
    {
        return match ($role) {
            'client'             => ['national_id_front', 'national_id_back', 'selfie_with_id'],
            'individual_owner'   => ['national_id_front', 'national_id_back', 'selfie_with_id', 'kra_pin_certificate'],
            'yard_owner'         => ['national_id_front', 'national_id_back', 'selfie_with_id', 'business_certificate', 'kra_pin_certificate'],
            'broker'             => ['national_id_front', 'national_id_back', 'selfie_with_id', 'broker_registration'],
            'driver'             => ['national_id_front', 'national_id_back', 'driving_licence', 'police_clearance'],
            default              => ['national_id_front', 'national_id_back', 'selfie_with_id'],
        };
    }
}
