<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['client', 'listing', 'operator'])->latest();

        if ($s = $request->status) {
            $query->where('status', $s);
        }

        if ($q = $request->q) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('client', fn($u) => $u->where('name', 'like', "%$q%"))
                    ->orWhere('booking_ref', 'like', "%$q%");
            });
        }

        $bookings = $query->paginate(25)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['client', 'listing.user', 'listing.category', 'operator', 'payments', 'dispute']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function releaseEscrow(Booking $booking)
    {
        abort_unless($booking->status === 'completed', 403);

        $listing = $booking->listing;
        $owner = $listing->user;
        $fee = $booking->platform_fee ?? ($booking->total_amount * 0.10);
        $ownerAmount = $booking->total_amount - $fee;

        $owner->wallet?->credit(
            $ownerAmount,
            'booking_payout',
            'Payout for booking ' . $booking->booking_ref,
            $booking->id
        );

        $booking->update(['escrow_released_at' => now()]);

        return back()->with('success', 'Escrow released. KES ' . number_format($ownerAmount) . ' credited to owner wallet.');
    }
}
