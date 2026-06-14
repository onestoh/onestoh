<?php

namespace App\Http\Controllers;

use App\Models\KycDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function index()
    {
        $documents = auth()->user()->kycDocuments->groupBy('document_type');

        return view('kyc.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => ['required', 'string'],
            'file'          => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $user = auth()->user();
        $path = $request->file('file')->store("kyc/{$user->id}", 'public');

        KycDocument::updateOrCreate(
            ['user_id' => $user->id, 'document_type' => $request->document_type],
            ['file_path' => $path, 'status' => 'pending', 'admin_notes' => null]
        );

        return back()->with('success', 'Document uploaded successfully and is pending review.');
    }

    public function pending()
    {
        return view('kyc.pending');
    }
}
