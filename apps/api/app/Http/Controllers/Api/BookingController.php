<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\Dispute;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Booking::with(['asset:id,make,model,category,pickup_county', 'asset.primaryPhoto'])
            ->where('client_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function show(string $id): JsonResponse
    {
        $booking = Booking::with([
            'asset.media',
            'client:id,name,phone',
            'driver:id,name,phone',
            'payments',
            'escrow',
            'reviews',
            'dispute',
        ])->findOrFail($id);

        return response()->json($booking);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'asset_id'      => 'required|integer|exists:assets,id',
            'rental_type'   => 'required|in:self_drive,chauffeur',
            'duration_type' => 'required|in:hourly,daily,weekly,monthly',
            'start_at'      => 'required|date|after:now',
            'end_at'        => 'required|date|after:start_at',
            'pickup_address'=> 'sometimes|string|max:255',
            'referral_code' => 'sometimes|string|size:12',
            'client_notes'  => 'sometimes|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset = Asset::where('is_published', true)->where('status', 'active')->findOrFail($request->asset_id);

        try {
            $booking = $this->bookingService->createBooking($request->user(), $asset, $request->all());
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message'    => 'Booking created. Proceed to payment.',
            'booking'    => $booking,
            'pay_amount' => $booking->total_amount,
        ], 201);
    }

    public function advanceStatus(Request $request, string $id): JsonResponse
    {
        $booking = Booking::findOrFail($id);
        $user    = $request->user();

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->bookingService->advanceStatus($booking, $request->status, $user);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Status updated.', 'booking' => $booking->fresh()]);
    }

    public function cancel(Request $request, string $id): JsonResponse
    {
        $booking = Booking::where('client_id', $request->user()->id)->findOrFail($id);

        if ($booking->isCancelled()) {
            return response()->json(['message' => 'Booking already cancelled.'], 409);
        }

        if ($booking->isCompleted()) {
            return response()->json(['message' => 'Cannot cancel a completed booking.'], 409);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->bookingService->cancelBooking($booking, $request->user(), $request->reason);

        return response()->json(['message' => 'Booking cancelled.']);
    }

    public function ownerBookings(Request $request): JsonResponse
    {
        $assetIds = $request->user()->assets()->pluck('id');

        $bookings = Booking::with(['asset:id,make,model', 'client:id,name,phone'])
            ->whereIn('asset_id', $assetIds)
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return response()->json($bookings);
    }

    public function raiseDispute(Request $request, string $id): JsonResponse
    {
        $booking = Booking::findOrFail($id);
        $user    = $request->user();

        if (!in_array($user->id, [$booking->client_id, $booking->asset->owner_id])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($booking->dispute) {
            return response()->json(['message' => 'A dispute already exists for this booking.'], 409);
        }

        $validator = Validator::make($request->all(), [
            'type'        => 'required|in:damage,not_as_described,no_show,mileage_overage,deposit_retention,early_termination,other',
            'description' => 'required|string|max:2000',
            'evidence'    => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dispute = Dispute::create([
            'booking_id'  => $booking->id,
            'raised_by'   => $user->id,
            'type'        => $request->type,
            'description' => $request->description,
            'evidence'    => $request->evidence ?? [],
        ]);

        $booking->update(['status' => 'disputed']);

        // Freeze escrow
        app(\App\Services\EscrowService::class)->freezeForDispute($booking);

        return response()->json(['message' => 'Dispute raised.', 'dispute' => $dispute], 201);
    }

    public function submitPreRentalPhotos(Request $request, string $id): JsonResponse
    {
        $booking = Booking::where('client_id', $request->user()->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'photos'   => 'required|array|min:3|max:10',
            'photos.*' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $paths = [];
        foreach ($request->file('photos') as $photo) {
            $paths[] = \Illuminate\Support\Facades\Storage::disk('s3')->putFile("bookings/{$booking->id}/pre", $photo, 'private');
        }

        $booking->update(['pre_rental_photos' => $paths]);

        return response()->json(['message' => 'Pre-rental photos uploaded.', 'photos' => $paths]);
    }

    public function submitPostRentalPhotos(Request $request, string $id): JsonResponse
    {
        $booking = Booking::where('client_id', $request->user()->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'photos'   => 'required|array|min:3|max:10',
            'photos.*' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $paths = [];
        foreach ($request->file('photos') as $photo) {
            $paths[] = \Illuminate\Support\Facades\Storage::disk('s3')->putFile("bookings/{$booking->id}/post", $photo, 'private');
        }

        $booking->update(['post_rental_photos' => $paths]);

        return response()->json(['message' => 'Post-rental photos uploaded.', 'photos' => $paths]);
    }
}
