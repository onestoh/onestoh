<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\Yard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    public function index()
    {
        $listings = Listing::where('user_id', auth()->id())
            ->with(['category', 'primaryPhoto'])
            ->withTrashed()
            ->latest()
            ->paginate(15);

        return view('listings.index', compact('listings'));
    }

    public function create()
    {
        $categories = AssetCategory::where('is_active', true)->orderBy('sort_order')->get();
        $yards = Yard::where('user_id', auth()->id())->get();
        return view('listings.create', compact('categories', 'yards'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'                  => ['required', 'string', 'max:200'],
            'description'            => ['required', 'string'],
            'asset_category_id'      => ['required', 'exists:asset_categories,id'],
            'yard_id'                => ['nullable', 'exists:yards,id'],
            'make'                   => ['nullable', 'string', 'max:100'],
            'model'                  => ['nullable', 'string', 'max:100'],
            'year'                   => ['nullable', 'integer', 'min:1970', 'max:' . (date('Y') + 1)],
            'registration_plate'     => ['nullable', 'string', 'max:20'],
            'fuel_type'              => ['nullable', 'in:petrol,diesel,electric,hybrid,other'],
            'transmission'           => ['nullable', 'in:manual,automatic'],
            'seats'                  => ['nullable', 'integer', 'min:1'],
            'load_capacity'          => ['nullable', 'string', 'max:50'],
            'drive_mode'             => ['required', 'in:self_drive,with_driver,either'],
            'listing_mode'           => ['required', 'in:rental,sale,both'],
            'county'                 => ['required', 'string', 'max:100'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'hourly_rate'            => ['nullable', 'numeric', 'min:0'],
            'daily_rate'             => ['nullable', 'numeric', 'min:0'],
            'weekly_rate'            => ['nullable', 'numeric', 'min:0'],
            'monthly_rate'           => ['nullable', 'numeric', 'min:0'],
            'sale_price'             => ['nullable', 'numeric', 'min:0'],
            'security_deposit'       => ['nullable', 'numeric', 'min:0'],
            'driver_surcharge_daily' => ['nullable', 'numeric', 'min:0'],
            'features'               => ['nullable', 'string'],
            'photos'                 => ['nullable', 'array'],
            'photos.*'               => ['file', 'image', 'max:5120'],
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';
        $data['category_id'] = $data['asset_category_id'];
        unset($data['asset_category_id']);

        if (!empty($data['features'])) {
            $data['features'] = array_values(array_filter(array_map('trim', explode(',', $data['features']))));
        }

        $listing = Listing::create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $i => $photo) {
                $path = $photo->store("listings/{$listing->id}", 'public');
                ListingPhoto::create([
                    'listing_id' => $listing->id,
                    'file_path'  => $path,
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('listings.index')->with('success', 'Listing submitted for review. It will go live once approved.');
    }

    public function show(Listing $listing)
    {
        abort_unless($listing->user_id === auth()->id(), 403);
        $listing->load(['category', 'photos', 'bookings' => fn($q) => $q->latest()->limit(10)]);
        return view('listings.show', compact('listing'));
    }

    public function edit(Listing $listing)
    {
        abort_unless($listing->user_id === auth()->id(), 403);
        $categories = AssetCategory::where('is_active', true)->orderBy('sort_order')->get();
        $yards = Yard::where('user_id', auth()->id())->get();
        return view('listings.edit', compact('listing', 'categories', 'yards'));
    }

    public function update(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === auth()->id(), 403);

        $data = $request->validate([
            'title'                  => ['required', 'string', 'max:200'],
            'description'            => ['required', 'string'],
            'asset_category_id'      => ['required', 'exists:asset_categories,id'],
            'yard_id'                => ['nullable', 'exists:yards,id'],
            'make'                   => ['nullable', 'string', 'max:100'],
            'model'                  => ['nullable', 'string', 'max:100'],
            'year'                   => ['nullable', 'integer', 'min:1970', 'max:' . (date('Y') + 1)],
            'registration_plate'     => ['nullable', 'string', 'max:20'],
            'fuel_type'              => ['nullable', 'in:petrol,diesel,electric,hybrid,other'],
            'transmission'           => ['nullable', 'in:manual,automatic'],
            'seats'                  => ['nullable', 'integer', 'min:1'],
            'load_capacity'          => ['nullable', 'string', 'max:50'],
            'drive_mode'             => ['required', 'in:self_drive,with_driver,either'],
            'listing_mode'           => ['required', 'in:rental,sale,both'],
            'county'                 => ['required', 'string', 'max:100'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'hourly_rate'            => ['nullable', 'numeric', 'min:0'],
            'daily_rate'             => ['nullable', 'numeric', 'min:0'],
            'weekly_rate'            => ['nullable', 'numeric', 'min:0'],
            'monthly_rate'           => ['nullable', 'numeric', 'min:0'],
            'sale_price'             => ['nullable', 'numeric', 'min:0'],
            'security_deposit'       => ['nullable', 'numeric', 'min:0'],
            'driver_surcharge_daily' => ['nullable', 'numeric', 'min:0'],
            'features'               => ['nullable', 'string'],
            'photos'                 => ['nullable', 'array'],
            'photos.*'               => ['file', 'image', 'max:5120'],
        ]);

        if (!empty($data['features'])) {
            $data['features'] = array_values(array_filter(array_map('trim', explode(',', $data['features']))));
        }

        if (isset($data['asset_category_id'])) {
            $data['category_id'] = $data['asset_category_id'];
            unset($data['asset_category_id']);
        }

        $listing->update($data);

        if ($request->hasFile('photos')) {
            $existingCount = $listing->photos()->count();
            foreach ($request->file('photos') as $i => $photo) {
                $path = $photo->store("listings/{$listing->id}", 'public');
                ListingPhoto::create([
                    'listing_id' => $listing->id,
                    'file_path'  => $path,
                    'is_primary' => $existingCount === 0 && $i === 0,
                    'sort_order' => $existingCount + $i,
                ]);
            }
        }

        return redirect()->route('listings.index')->with('success', 'Listing updated successfully.');
    }

    public function destroy(Listing $listing)
    {
        abort_unless($listing->user_id === auth()->id(), 403);
        $listing->delete();
        return back()->with('success', 'Listing removed.');
    }

    public function deletePhoto(Listing $listing, ListingPhoto $photo)
    {
        abort_unless($listing->user_id === auth()->id(), 403);
        abort_unless($photo->listing_id === $listing->id, 403);

        Storage::disk('public')->delete($photo->file_path);
        $wasPrimary = $photo->is_primary;
        $photo->delete();

        if ($wasPrimary) {
            $listing->photos()->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Photo removed.');
    }
}
