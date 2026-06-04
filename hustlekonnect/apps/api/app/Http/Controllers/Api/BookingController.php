<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function store(CreateBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->create($request->user(), $request->validated());
            return response()->json(['message' => 'Booking created.', 'data' => $booking], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with(['asset:id,title,daily_rate,asset_type', 'payments'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return response()->json($bookings);
    }

    public function show(string $id): JsonResponse
    {
        $booking = Booking::with(['asset.yard', 'payments', 'escrow', 'driver'])
            ->where('user_id', request()->user()->id)
            ->findOrFail($id);

        return response()->json(['data' => $booking]);
    }

    public function cancel(Request $request, string $id): JsonResponse
    {
        $booking = Booking::where('user_id', $request->user()->id)->findOrFail($id);

        try {
            $this->bookingService->cancel($booking, $request->get('reason', ''));
            return response()->json(['message' => 'Booking cancelled.']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function ownerBookings(Request $request): JsonResponse
    {
        $bookings = Booking::with(['user:id,name,phone,trust_score', 'asset:id,title'])
            ->whereHas('asset.yard', fn($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(15);

        return response()->json($bookings);
    }
}
