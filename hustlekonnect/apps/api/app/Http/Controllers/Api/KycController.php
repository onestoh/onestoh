<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'document_type'   => 'required|in:national_id,passport,drivers_license,business_reg',
            'document_number' => 'required|string|max:50',
            'country'         => 'required|string|size:2',
            'document_file'   => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        $file = $request->file('document_file');
        $path = $file->store("kyc/{$request->user()->id}", 's3');

        $doc = KycDocument::updateOrCreate(
            ['user_id' => $request->user()->id, 'document_type' => $request->document_type],
            [
                'document_number' => $request->document_number,
                'country'         => $request->country,
                'file_path'       => $path,
                'status'          => 'pending',
            ]
        );

        $request->user()->update(['kyc_status' => 'pending']);

        return response()->json(['message' => 'KYC document submitted for review.', 'data' => $doc], 201);
    }

    public function status(Request $request): JsonResponse
    {
        $docs = KycDocument::where('user_id', $request->user()->id)->get();
        return response()->json([
            'data' => [
                'kyc_status' => $request->user()->kyc_status,
                'documents'  => $docs,
            ]
        ]);
    }
}
