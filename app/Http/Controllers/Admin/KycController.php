<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VerificationStatusChanged;
use App\Models\Verification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KycController extends Controller
{
    public function index()
    {
        $verifications = Verification::with(['user', 'documents'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.kyc.index', compact('verifications'));
    }

    public function approve($id)
    {
        $verification = Verification::with('user')->findOrFail($id);
        $user         = $verification->user;

        $verification->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'reviewed_by' => session('user_id'),
        ]);

        $user?->update([
            'is_verified'         => true,
            'verification_tier'   => $verification->type ?? 'standard',
        ]);

        try {
            if ($user?->email) {
                Mail::to($user->email)->queue(new VerificationStatusChanged($verification, 'approved'));
            }
            NotificationService::send(
                $user->id,
                'Verification Approved',
                'Your identity verification has been approved. You now have a verified badge.',
                'document',
                '/dashboard/verification'
            );
        } catch (\Throwable $e) {
            Log::warning('KYC approve mail failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.kyc.index')->with('success', "Verification #{$id} approved.");
    }

    public function reject($id)
    {
        $verification = Verification::with('user')->findOrFail($id);
        $user         = $verification->user;

        $verification->update([
            'status'      => 'rejected',
            'reviewed_by' => session('user_id'),
        ]);

        try {
            if ($user?->email) {
                Mail::to($user->email)->queue(new VerificationStatusChanged($verification, 'rejected'));
            }
            NotificationService::send(
                $user->id,
                'Verification Rejected',
                'Your verification submission was rejected. Please resubmit with valid documents.',
                'document',
                '/dashboard/verification'
            );
        } catch (\Throwable $e) {
            Log::warning('KYC reject mail failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.kyc.index')->with('error', "Verification #{$id} rejected.");
    }
}
