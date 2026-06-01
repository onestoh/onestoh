<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminListingController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function pending(Request $request): JsonResponse
    {
        $listings = Asset::with(['owner:id,name,email,kyc_status', 'media'])
            ->where('is_published', false)
            ->whereNull('admin_approved_at')
            ->whereHas('owner', fn($q) => $q->where('kyc_status', 'approved'))
            ->latest()
            ->paginate(20);

        return response()->json($listings);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);

        if (!$asset->hasMinimumPhotos()) {
            return response()->json(['message' => 'Asset needs at least 5 photos before approval.'], 422);
        }

        if (!$asset->hasActiveRate() && $asset->listing_mode !== 'sale_only') {
            return response()->json(['message' => 'Asset must have at least one rental rate set.'], 422);
        }

        $asset->update([
            'is_published'      => true,
            'admin_approved_at' => now(),
            'approved_by'       => $request->user()->id,
        ]);

        // Increment yard asset count
        if ($asset->yard_id) {
            $asset->yard->increment('total_assets');
        }

        $this->notifications->send($asset->owner, 'listing_approved', [
            'asset_name' => "{$asset->make} {$asset->model}",
        ]);

        return response()->json(['message' => 'Listing approved and published.']);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), ['reason' => 'required|string|max:500']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset = Asset::findOrFail($id);
        // Store rejection note in description field or a dedicated column
        $asset->update(['is_published' => false]);

        $this->notifications->send($asset->owner, 'listing_rejected', [
            'asset_name' => "{$asset->make} {$asset->model}",
            'reason'     => $request->reason,
        ]);

        return response()->json(['message' => 'Listing rejected.']);
    }

    public function unpublish(Request $request, int $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);
        $asset->update(['is_published' => false]);

        return response()->json(['message' => 'Listing unpublished.']);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Asset::with(['owner:id,name', 'yard:id,name'])->withTrashed();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('county')) $query->where('pickup_county', $request->county);
        if ($request->filled('is_published')) $query->where('is_published', $request->boolean('is_published'));
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('make', 'like', "%{$s}%")->orWhere('model', 'like', "%{$s}%"));
        }

        return response()->json($query->latest()->paginate(25));
    }
}
