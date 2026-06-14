<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use App\Models\User;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
    public function index()
    {
        $pendingDocs = KycDocument::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->groupBy('user_id');

        return view('admin.kyc.index', compact('pendingDocs'));
    }

    public function approve(KycDocument $document)
    {
        $document->update(['status' => 'approved']);

        // Check if all docs approved → verify user
        $user = $document->user;
        $allApproved = $user->kycDocuments()->where('status', '!=', 'approved')->doesntExist();
        if ($allApproved && $user->kycDocuments()->count() >= 2) {
            $user->update(['status' => 'verified']);
        }

        return back()->with('success', 'Document approved.');
    }

    public function reject(Request $request, KycDocument $document)
    {
        $request->validate(['notes' => ['required', 'string', 'min:5']]);
        $document->update(['status' => 'rejected', 'admin_notes' => $request->notes]);
        return back()->with('success', 'Document rejected with feedback.');
    }
}
