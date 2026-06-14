<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Listing;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bookings = Booking::with('listing.primaryPhoto')
            ->where(function ($q) use ($user) {
                $q->where('client_id', $user->id)
                  ->orWhereHas('listing', fn($lq) => $lq->where('user_id', $user->id));
            })
            ->latest()
            ->paginate(15);

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $listing = Listing::with(['photos', 'category'])->active()->findOrFail($request->listing_id);
        $durationType = $request->get('duration_type', 'daily');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        return view('bookings.create', compact('listing', 'durationType', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'listing_id'    => ['required', 'exists:listings,id'],
            'duration_type' => ['required', 'in:hourly,daily,weekly,monthly'],
            'start_datetime' => ['required', 'date', 'after:now'],
            'end_datetime'   => ['required', 'date', 'after:start_datetime'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $listing = Listing::active()->findOrFail($data['listing_id']);

        $start = \Carbon\Carbon::parse($data['start_datetime']);
        $end = \Carbon\Carbon::parse($data['end_datetime']);

        $baseAmount = $this->calculateAmount($listing, $data['duration_type'], $start, $end);
        $platformFeeRate = PlatformSetting::get('platform_fee_percentage', 10) / 100;
        $platformFee = round($baseAmount * $platformFeeRate, 2);
        $securityDeposit = $listing->security_deposit ?? 0;
        $totalAmount = $baseAmount + $platformFee + $securityDeposit;

        $booking = Booking::create([
            'listing_id'      => $listing->id,
            'client_id'       => auth()->id(),
            'duration_type'   => $data['duration_type'],
            'start_datetime'  => $start,
            'end_datetime'    => $end,
            'base_amount'     => $baseAmount,
            'platform_fee'    => $platformFee,
            'security_deposit' => $securityDeposit,
            'total_amount'    => $totalAmount,
            'status'          => 'pending_payment',
            'notes'           => $data['notes'] ?? null,
            'slot_hold_expires_at' => now()->addMinutes(15),
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking created! Complete payment within 15 minutes to confirm your slot.');
    }

    public function show(Booking $booking)
    {
        abort_unless(
            $booking->client_id === auth()->id() || $booking->listing?->user_id === auth()->id(),
            403
        );
        $booking->load(['listing.photos', 'listing.user', 'listing.category']);
        return view('bookings.show', compact('booking'));
    }

    private function calculateAmount(Listing $listing, string $durationType, \Carbon\Carbon $start, \Carbon\Carbon $end): float
    {
        return match($durationType) {
            'hourly'  => ceil($start->diffInHours($end)) * ($listing->hourly_rate ?? 0),
            'weekly'  => ceil($start->diffInDays($end) / 7) * ($listing->weekly_rate ?? 0),
            'monthly' => ceil($start->diffInDays($end) / 30) * ($listing->monthly_rate ?? 0),
            default   => ceil($start->diffInDays($end)) * ($listing->daily_rate ?? 0),
        };
    }

    public function cancel(Booking $booking)
    {
        abort_unless($booking->client_id === auth()->id(), 403);
        abort_unless(in_array($booking->status, ['pending_payment', 'confirmed']), 403);

        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking cancelled.');
    }
}
