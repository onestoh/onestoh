<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\Request;

class AdminDisputeController extends Controller
{
    public function index()
    {
        $disputes = Dispute::with(['booking.client', 'booking.listing'])->latest()->paginate(20);

        return view('admin.disputes.index', compact('disputes'));
    }

    public function show(Dispute $dispute)
    {
        $dispute->load(['booking.client', 'booking.listing.user', 'booking.payments']);

        return view('admin.disputes.show', compact('dispute'));
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        $request->validate([
            'resolution' => 'required|string',
            'outcome'    => 'required|in:refund_client,release_owner,split',
        ]);

        $dispute->update([
            'resolution'  => $request->resolution,
            'status'      => 'resolved',
            'resolved_at' => now(),
        ]);

        $booking = $dispute->booking;

        if ($request->outcome === 'refund_client') {
            $booking->client?->wallet?->credit(
                $booking->total_amount,
                'dispute_refund',
                'Dispute resolved: refund',
                $booking->id
            );
            $booking->update(['status' => 'cancelled']);
        } elseif ($request->outcome === 'release_owner') {
            $owner = $booking->listing?->user;
            $fee   = $booking->platform_fee ?? ($booking->total_amount * 0.10);
            $owner?->wallet?->credit(
                $booking->total_amount - $fee,
                'dispute_payout',
                'Dispute resolved: owner payout',
                $booking->id
            );
            $booking->update(['status' => 'completed']);
        } else {
            // split: 50/50
            $half  = $booking->total_amount / 2;
            $owner = $booking->listing?->user;
            $booking->client?->wallet?->credit($half, 'dispute_split', 'Dispute split refund', $booking->id);
            $owner?->wallet?->credit($half, 'dispute_split', 'Dispute split payout', $booking->id);
            $booking->update(['status' => 'completed']);
        }

        return back()->with('success', 'Dispute resolved.');
    }
}
