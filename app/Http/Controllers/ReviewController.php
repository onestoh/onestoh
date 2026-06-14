<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        abort_unless($booking->client_id === auth()->id(), 403);
        abort_unless($booking->status === 'completed', 403);
        abort_if(
            Review::where('booking_id', $booking->id)->where('reviewer_id', auth()->id())->exists(),
            422,
            'Already reviewed.'
        );

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'booking_id'     => $booking->id,
            'reviewer_id'    => auth()->id(),
            'reviewee_id'    => $booking->listing?->user_id,
            'listing_id'     => $booking->listing_id,
            'overall_rating' => $request->rating,
            'comment'        => $request->comment,
            'is_public'      => true,
        ]);

        return back()->with('success', 'Review submitted. Thank you!');
    }
}
