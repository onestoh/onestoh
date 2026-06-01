<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingReview;
use App\Models\HotelRoom;
use App\Models\Property;
use App\Services\BookingService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * GET /api/search/availability — check availability and pricing (AJAX public)
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'check_in'    => 'required|date',
            'check_out'   => 'required|date|after:check_in',
            'room_id'     => 'nullable|integer|exists:hotel_rooms,id',
        ]);

        $available = BookingService::isAvailable(
            $request->property_id,
            $request->check_in,
            $request->check_out,
            $request->room_id
        );

        $pricing = BookingService::calculatePrice(
            $request->property_id,
            $request->check_in,
            $request->check_out,
            $request->room_id
        );

        $blockedDates = BookingService::getBlockedDates($request->property_id, $request->room_id);

        return response()->json([
            'available'     => $available,
            'pricing'       => $pricing,
            'blocked_dates' => $blockedDates,
        ]);
    }

    /**
     * GET /bookings/availability/{propertyId} — return blocked dates as JSON (public)
     */
    public function blockedDates($propertyId)
    {
        $dates = BookingService::getBlockedDates((int) $propertyId);
        return response()->json($dates);
    }

    /**
     * GET /properties/{id}/book — show booking form
     */
    public function create(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $rooms = [];
        if (in_array($property->listing_type, ['hotel'])) {
            $rooms = HotelRoom::where('property_id', $id)->where('is_active', true)->get();
        }

        $checkIn  = $request->get('check_in', '');
        $checkOut = $request->get('check_out', '');
        $guests   = (int) $request->get('guests', 1);

        $pricing = null;
        if ($checkIn && $checkOut) {
            try {
                $pricing = BookingService::calculatePrice($property->id, $checkIn, $checkOut);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return view('bookings.create', compact('property', 'rooms', 'checkIn', 'checkOut', 'guests', 'pricing'));
    }

    /**
     * POST /bookings — store a new booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id'      => 'required|integer|exists:properties,id',
            'check_in'         => 'required|date|after_or_equal:today',
            'check_out'        => 'required|date|after:check_in',
            'guests_count'     => 'required|integer|min:1|max:20',
            'room_id'          => 'nullable|integer|exists:hotel_rooms,id',
            'payment_method'   => 'required|in:mpesa,bank',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $userId   = session('user_id');
        $property = Property::findOrFail($request->property_id);

        // Re-check availability
        if (!BookingService::isAvailable($request->property_id, $request->check_in, $request->check_out, $request->room_id)) {
            return redirect()->back()->withErrors(['check_in' => 'Selected dates are not available. Please choose different dates.'])->withInput();
        }

        $bookingType = in_array($property->listing_type, ['hotel', 'airbnb']) ? $property->listing_type : 'airbnb';

        $booking = BookingService::create([
            'property_id'      => $request->property_id,
            'room_id'          => $request->room_id,
            'guest_id'         => $userId,
            'type'             => $bookingType,
            'check_in'         => $request->check_in,
            'check_out'        => $request->check_out,
            'guests_count'     => $request->guests_count,
            'special_requests' => $request->special_requests,
        ]);

        return redirect()->route('booking.payment', $booking->id);
    }

    /**
     * GET /bookings/{id}/payment — show payment page
     */
    public function payment($id)
    {
        $userId  = session('user_id');
        $booking = Booking::with('property', 'room')->findOrFail($id);

        if ($booking->guest_id != $userId) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('booking.confirmation', $id);
        }

        return view('bookings.payment', compact('booking'));
    }

    /**
     * POST /bookings/{id}/payment — process simulated payment
     */
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:mpesa,bank',
            'phone'          => 'nullable|string|max:20',
        ]);

        $userId  = session('user_id');
        $booking = Booking::findOrFail($id);

        if ($booking->guest_id != $userId) {
            abort(403);
        }

        $method = $request->payment_method;
        $ref    = strtoupper($method) . rand(100000, 999999);

        try {
            BookingService::confirm($booking, $method, $ref);
        } catch (\Throwable $e) {
            Log::error('BookingService::confirm failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Payment processing failed. Please try again.']);
        }

        return redirect()->route('booking.confirmation', $id);
    }

    /**
     * GET /bookings/{id}/confirmation
     */
    public function confirmation($id)
    {
        $userId  = session('user_id');
        $booking = Booking::with('property', 'room', 'guest')->findOrFail($id);

        if ($booking->guest_id != $userId) {
            abort(403);
        }

        return view('bookings.confirmation', compact('booking'));
    }

    /**
     * POST /bookings/{id}/cancel
     */
    public function cancel(Request $request, $id)
    {
        $userId  = session('user_id');
        $booking = Booking::findOrFail($id);

        if ($booking->guest_id != $userId && $booking->host_id != $userId) {
            abort(403);
        }

        $reason = $request->get('reason', '');
        BookingService::cancel($booking, $reason);

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * GET /dashboard/bookings — guest's bookings
     */
    public function myBookings()
    {
        $userId   = session('user_id');
        $bookings = Booking::with('property', 'room')
            ->where('guest_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $upcoming  = $bookings->whereIn('status', ['pending', 'confirmed', 'paid', 'checked_in'])->values();
        $past      = $bookings->whereIn('status', ['checked_out'])->values();
        $cancelled = $bookings->whereIn('status', ['cancelled', 'refunded'])->values();

        return view('bookings.my-bookings', compact('bookings', 'upcoming', 'past', 'cancelled'));
    }

    /**
     * GET /dashboard/host-bookings — landlord's incoming bookings
     */
    public function myHostBookings()
    {
        $userId   = session('user_id');
        $bookings = Booking::with('property', 'room', 'guest')
            ->where('host_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return view('bookings.host-bookings', compact('bookings'));
    }

    /**
     * POST /bookings/{id}/review — store review after checkout
     */
    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating'        => 'required|integer|min:1|max:5',
            'cleanliness'   => 'nullable|integer|min:1|max:5',
            'communication' => 'nullable|integer|min:1|max:5',
            'location'      => 'nullable|integer|min:1|max:5',
            'value'         => 'nullable|integer|min:1|max:5',
            'comment'       => 'nullable|string|max:2000',
        ]);

        $userId  = session('user_id');
        $booking = Booking::findOrFail($id);

        if ($booking->guest_id != $userId) {
            abort(403);
        }

        BookingReview::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'reviewer_id'   => $userId,
                'property_id'   => $booking->property_id,
                'rating'        => $request->rating,
                'cleanliness'   => $request->cleanliness,
                'communication' => $request->communication,
                'location'      => $request->location,
                'value'         => $request->value,
                'comment'       => $request->comment,
                'is_published'  => true,
            ]
        );

        return redirect()->back()->with('success', 'Review submitted. Thank you!');
    }

    /**
     * POST /bookings/{id}/checkin
     */
    public function checkIn(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'checked_in']);

        NotificationService::send($booking->host_id, 'Guest Checked In',
            optional($booking->guest)->name . ' has checked in to ' . optional($booking->property)->title . ' · ' . now()->format('d M Y H:i'),
            'system', '/dashboard/host-bookings');
        NotificationService::send($booking->guest_id, 'Welcome! Check-in Confirmed',
            'You have successfully checked in to ' . optional($booking->property)->title . '. Enjoy your stay!',
            'system', '/dashboard/bookings');

        return response()->json(['success' => true, 'message' => 'Checked in successfully']);
    }

    /**
     * POST /bookings/{id}/checkout
     */
    public function checkOut(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'checked_out']);

        NotificationService::send($booking->host_id, 'Guest Checked Out',
            optional($booking->guest)->name . ' checked out of ' . optional($booking->property)->title . '. Please review their stay.',
            'system', '/dashboard/host-bookings');
        NotificationService::send($booking->guest_id, 'How was your stay?',
            'Thank you for staying at ' . optional($booking->property)->title . '. Please leave a review!',
            'system', '/bookings/' . $booking->id . '/review');

        return response()->json(['success' => true, 'message' => 'Checked out successfully']);
    }
}
