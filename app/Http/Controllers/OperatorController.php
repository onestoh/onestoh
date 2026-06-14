<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function assignments()
    {
        $user = auth()->user();

        $assignments = Booking::with(['listing.photos', 'listing.yard', 'client'])
            ->where('operator_id', $user->id)
            ->orderByDesc('start_datetime')
            ->paginate(15);

        $stats = [
            'active'    => Booking::where('operator_id', $user->id)->where('status', 'active')->count(),
            'upcoming'  => Booking::where('operator_id', $user->id)->where('status', 'confirmed')->where('start_datetime', '>', now())->count(),
            'completed' => Booking::where('operator_id', $user->id)->where('status', 'completed')->count(),
        ];

        return view('operator.assignments', compact('assignments', 'stats', 'user'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        abort_unless($booking->operator_id === auth()->id(), 403);
        // Operator can only mark active → completed
        if ($booking->status === 'active' && $request->status === 'completed') {
            $booking->update(['status' => 'completed']);
        }
        return back()->with('success', 'Status updated.');
    }
}
