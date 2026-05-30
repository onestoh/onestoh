<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\VerificationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'tier'        => 'required|in:basic,professional,elite',
            'documents'   => 'nullable|array|max:10',
            'documents.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $userId = session('user_id');

        $verification = Verification::updateOrCreate(
            ['user_id' => $userId],
            [
                'tier'         => $request->tier,
                'status'       => 'pending',
                'submitted_at' => now(),
            ]
        );

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $path = $doc->store("verifications/{$userId}", 'public');

                VerificationDocument::create([
                    'verification_id' => $verification->id,
                    'document_type'   => $doc->getClientOriginalExtension(),
                    'file_path'       => $path,
                    'original_name'   => $doc->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Verification documents submitted. Our team will review within 2-3 business days.');
    }
}
