<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'asset_id'   => 'sometimes|integer|exists:assets,id',
            'user_id'    => 'sometimes|integer|exists:users,id',
            'reviewer_type' => 'sometimes|in:client,owner',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = Review::with(['reviewer:id,name', 'booking:id,asset_id,start_at'])
            ->where('is_published', true);

        if ($request->filled('asset_id')) {
            $query->whereHas('booking', fn($q) => $q->where('asset_id', $request->asset_id));
        }
        if ($request->filled('user_id')) {
            $query->where('reviewee_id', $request->user_id);
        }
        if ($request->filled('reviewer_type')) {
            $query->where('reviewer_type', $request->reviewer_type);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_id'     => 'required|uuid|exists:bookings,id',
            'overall_rating' => 'required|integer|min:1|max:5',
            'sub_ratings'    => 'sometimes|array',
            'sub_ratings.*'  => 'integer|min:1|max:5',
            'comment'        => 'required|string|min:20|max:1000',
            'tags'           => 'sometimes|array|max:5',
            'tags.*'         => 'string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user    = $request->user();
        $booking = Booking::findOrFail($request->booking_id);

        if (!$booking->isCompleted()) {
            return response()->json(['message' => 'Reviews can only be submitted after a booking is completed.'], 422);
        }

        $isClient = $booking->client_id === $user->id;
        $isOwner  = $booking->asset->owner_id === $user->id;

        if (!$isClient && !$isOwner) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $reviewerType = $isClient ? 'client' : 'owner';
        $revieweeId   = $isClient ? $booking->asset->owner_id : $booking->client_id;

        // One review per booking per reviewer
        if (Review::where('booking_id', $booking->id)->where('reviewer_id', $user->id)->exists()) {
            return response()->json(['message' => 'You have already reviewed this booking.'], 409);
        }

        $review = Review::create([
            'booking_id'     => $booking->id,
            'reviewer_id'    => $user->id,
            'reviewee_id'    => $revieweeId,
            'reviewer_type'  => $reviewerType,
            'overall_rating' => $request->overall_rating,
            'sub_ratings'    => $request->sub_ratings,
            'comment'        => $request->comment,
            'tags'           => $request->tags,
        ]);

        // Update average rating on asset and reviewee user
        $this->updateRatings($booking, $revieweeId);

        return response()->json(['message' => 'Review submitted.', 'review' => $review], 201);
    }

    public function ownerResponse(Request $request, int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $user   = $request->user();

        // Verify user is the reviewee/owner
        if ($review->reviewee_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($review->owner_response) {
            return response()->json(['message' => 'Response already submitted.'], 409);
        }

        $validator = Validator::make($request->all(), [
            'response' => 'required|string|min:10|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review->update([
            'owner_response'      => $request->response,
            'owner_responded_at'  => now(),
        ]);

        return response()->json(['message' => 'Response submitted.', 'review' => $review->fresh()]);
    }

    private function updateRatings(Booking $booking, int $revieweeId): void
    {
        // Update asset rating
        $asset = $booking->asset;
        $avgRating = Review::whereHas('booking', fn($q) => $q->where('asset_id', $asset->id))
            ->where('is_published', true)
            ->avg('overall_rating');
        $totalReviews = Review::whereHas('booking', fn($q) => $q->where('asset_id', $asset->id))
            ->where('is_published', true)
            ->count();
        $asset->update(['average_rating' => round($avgRating, 2), 'total_reviews' => $totalReviews]);

        // Update user rating
        $userAvg = Review::where('reviewee_id', $revieweeId)->where('is_published', true)->avg('overall_rating');
        User::where('id', $revieweeId)->update(['average_rating' => round($userAvg, 2)]);
    }
}
