<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Booking;
use App\Services\PhotoVerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PhotoVerificationController extends Controller
{
    public function __construct(private PhotoVerificationService $service) {}

    public function validateListing(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('update', $asset);

        $result = $this->service->validateListingPhotos($asset);

        return response()->json([
            'data'    => $result,
            'message' => $result['all_passed']
                ? 'All photos meet quality requirements.'
                : $result['total'] - count($result['passed']) . ' photo(s) did not meet quality requirements.',
        ]);
    }

    public function compareDamage(Request $request, Booking $booking): JsonResponse
    {
        $request->validate([
            'before_photo' => 'required|image|max:10240',
            'after_photo'  => 'required|image|max:10240',
        ]);

        // Only booking participants can submit damage comparison
        $user = $request->user();
        if ($booking->renter_id !== $user->id && $booking->asset->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beforePath = $request->file('before_photo')->store('temp');
        $afterPath  = $request->file('after_photo')->store('temp');

        $beforeFullPath = storage_path('app/' . $beforePath);
        $afterFullPath  = storage_path('app/' . $afterPath);

        $result = $this->service->compareDamage($beforeFullPath, $afterFullPath);

        // Cleanup temp files
        @unlink($beforeFullPath);
        @unlink($afterFullPath);

        return response()->json([
            'data'    => $result,
            'message' => $result['damage_detected']
                ? 'Potential damage detected. Please review and submit a dispute if required.'
                : 'No significant damage detected.',
        ]);
    }
}
