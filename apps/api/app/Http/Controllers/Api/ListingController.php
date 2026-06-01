<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ListingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Asset::with(['primaryPhoto', 'yard:id,name,verification_tier'])
            ->where('is_published', true)
            ->where('status', 'active');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('county')) {
            $query->where('pickup_county', $request->county);
        }
        if ($request->filled('min_rate')) {
            $query->where('daily_rate', '>=', $request->min_rate);
        }
        if ($request->filled('max_rate')) {
            $query->where('daily_rate', '<=', $request->max_rate);
        }
        if ($request->filled('rental_type')) {
            $field = $request->rental_type === 'chauffeur' ? 'is_chauffeur_enabled' : 'is_self_drive_enabled';
            $query->where($field, true);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($q2) => $q2->where('make', 'like', "%{$q}%")
                ->orWhere('model', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%"));
        }

        $listings = $query->select([
            'id', 'yard_id', 'owner_id', 'category', 'make', 'model', 'year',
            'fuel_type', 'transmission', 'seats', 'status', 'listing_mode',
            'is_self_drive_enabled', 'is_chauffeur_enabled',
            'daily_rate', 'hourly_rate', 'weekly_rate', 'monthly_rate', 'security_deposit',
            'pickup_county', 'pickup_area',
            'average_rating', 'total_reviews', 'total_completed_rentals',
        ])->paginate($request->per_page ?? 20);

        return response()->json($listings);
    }

    public function show(int $id): JsonResponse
    {
        $asset = Asset::with(['media', 'yard', 'owner:id,name,average_rating,trust_score,kyc_status'])
            ->where('is_published', true)
            ->findOrFail($id);

        return response()->json($asset);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->isOwner() && $user->role !== 'super_admin') {
            return response()->json(['message' => 'Only owners can create listings.'], 403);
        }
        if (!$user->isKycApproved()) {
            return response()->json(['message' => 'KYC approval required to list assets.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'category'          => 'required|in:passenger_car,suv_4x4,van_minibus,pickup_truck,heavy_truck,excavator,tractor_farm,crane_lift,generator,compactor_roller,motorcycle_tuktuk,special_equipment',
            'make'              => 'required|string|max:60',
            'model'             => 'required|string|max:80',
            'year'              => 'required|integer|min:1970|max:' . (date('Y') + 1),
            'registration_plate'=> 'required|string|max:20',
            'pickup_county'     => 'required|string|max:60',
            'pickup_area'       => 'required|string|max:100',
            'listing_mode'      => 'required|in:rent_only,sale_only,rent_and_sale',
            'daily_rate'        => 'required_if:listing_mode,rent_only,rent_and_sale|nullable|numeric|min:0',
            'sale_price'        => 'required_if:listing_mode,sale_only,rent_and_sale|nullable|numeric|min:0',
            'features'          => 'sometimes|array',
            'description'       => 'sometimes|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset = Asset::create(array_merge(
            $request->except(['registration_plate', 'vin']),
            [
                'owner_id'                     => $user->id,
                'registration_plate_encrypted' => Crypt::encryptString($request->registration_plate),
                'vin_encrypted'                => $request->vin ? Crypt::encryptString($request->vin) : null,
                'is_published'                 => false, // requires admin approval
            ]
        ));

        return response()->json(['message' => 'Asset created. Awaiting admin approval.', 'asset' => $asset], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $asset = Asset::where('owner_id', $request->user()->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'daily_rate'   => 'sometimes|numeric|min:0',
            'hourly_rate'  => 'sometimes|numeric|min:0',
            'weekly_rate'  => 'sometimes|numeric|min:0',
            'monthly_rate' => 'sometimes|numeric|min:0',
            'description'  => 'sometimes|string|max:2000',
            'features'     => 'sometimes|array',
            'status'       => 'sometimes|in:active,under_maintenance,off_road,retired',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset->update($request->only([
            'daily_rate', 'hourly_rate', 'weekly_rate', 'monthly_rate',
            'description', 'features', 'usage_rules', 'status',
            'is_self_drive_enabled', 'is_chauffeur_enabled', 'is_delivery_enabled',
            'delivery_fee_per_km', 'security_deposit', 'sale_price', 'sale_description',
        ]));

        return response()->json(['asset' => $asset->fresh()]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $asset = Asset::where('owner_id', $request->user()->id)->findOrFail($id);

        // Check no active bookings
        if ($asset->bookings()->whereIn('status', ['confirmed', 'active', 'owner_notified', 'client_prepared'])->exists()) {
            return response()->json(['message' => 'Cannot delete asset with active bookings.'], 409);
        }

        $asset->delete();
        return response()->json(['message' => 'Asset deleted.']);
    }

    public function uploadMedia(Request $request, int $id): JsonResponse
    {
        $asset = Asset::where('owner_id', $request->user()->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'files'    => 'required|array|min:1|max:10',
            'files.*'  => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:51200',
            'type'     => 'required|in:photo,video',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $photoCount = $asset->media()->where('type', 'photo')->count();
        $maxPhotos  = 20;

        $uploaded = [];
        foreach ($request->file('files') as $file) {
            if ($request->type === 'photo' && $photoCount >= $maxPhotos) break;

            $hash = md5_file($file->path());

            // Duplicate detection
            if (AssetMedia::where('file_hash', $hash)->exists()) {
                continue;
            }

            $path = Storage::disk('s3')->putFile("assets/{$asset->id}/media", $file, 'public');

            $sortOrder = $asset->media()->max('sort_order') + 1;
            $isPrimary = !$asset->media()->where('type', 'photo')->exists();

            $media = AssetMedia::create([
                'asset_id'   => $asset->id,
                'type'       => $request->type,
                'file_path'  => $path,
                'file_hash'  => $hash,
                'sort_order' => $sortOrder,
                'is_primary' => $isPrimary,
                'file_size'  => $file->getSize(),
            ]);

            $uploaded[] = $media;
            if ($request->type === 'photo') $photoCount++;
        }

        return response()->json(['message' => count($uploaded) . ' file(s) uploaded.', 'media' => $uploaded], 201);
    }

    public function deleteMedia(Request $request, int $assetId, int $mediaId): JsonResponse
    {
        $asset = Asset::where('owner_id', $request->user()->id)->findOrFail($assetId);
        $media = AssetMedia::where('asset_id', $asset->id)->findOrFail($mediaId);

        Storage::disk('s3')->delete($media->file_path);
        $media->delete();

        // Reassign primary if needed
        if ($media->is_primary) {
            $next = $asset->media()->where('type', 'photo')->oldest()->first();
            $next?->update(['is_primary' => true]);
        }

        return response()->json(['message' => 'Media deleted.']);
    }

    public function myListings(Request $request): JsonResponse
    {
        $assets = Asset::with('primaryPhoto')
            ->where('owner_id', $request->user()->id)
            ->withTrashed()
            ->paginate(20);
        return response()->json($assets);
    }
}
