<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayoutApproved;

class AdminPayoutController extends Controller
{
    public function index()
    {
        $payouts = PayoutRequest::with('user')->latest()->paginate(25);
        return view('admin.payouts.index', compact('payouts'));
    }

    public function approve(PayoutRequest $payout)
    {
        abort_unless($payout->status === 'pending', 403);
        $payout->update(['status' => 'approved', 'processed_at' => now()]);

        // Send payout approved email
        try {
            if ($payout->user?->email) {
                Mail::to($payout->user)->queue(new PayoutApproved($payout));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PayoutApproved email error: ' . $e->getMessage());
        }

        return back()->with('success', 'Payout approved. Transfer KES ' . number_format($payout->amount) . ' to ' . $payout->mpesa_number);
    }

    public function reject(Request $request, PayoutRequest $payout)
    {
        abort_unless($payout->status === 'pending', 403);
        $request->validate(['reason' => ['required', 'string']]);

        // Reverse the wallet debit
        $payout->user->wallet?->credit($payout->amount, 'payout_reversal', 'Payout rejected: ' . $request->reason, null);
        $payout->update(['status' => 'rejected', 'rejection_reason' => $request->reason]);

        return back()->with('success', 'Payout rejected and funds returned to wallet.');
    }
}
