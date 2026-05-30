<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    private array $propertyTypes = [
        'house', 'apartment', 'villa', 'bungalow', 'townhouse', 'studio',
        'office', 'commercial', 'warehouse', 'industrial', 'land', 'farm',
        'bedsitter', 'mansion', 'maisonette',
    ];

    private array $listingTypes = ['sale', 'rent', 'lease', 'auction', 'off_plan'];

    private array $counties = [
        'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Kiambu', 'Machakos',
        'Kajiado', 'Muranga', 'Nyeri', 'Meru', 'Embu', 'Laikipia',
        'Nyandarua', 'Kirinyaga', 'Tharaka-Nithi', 'Isiolo', 'Marsabit',
        'Garissa', 'Wajir', 'Mandera', 'Turkana', 'Samburu', 'Trans-Nzoia',
        'West Pokot', 'Elgeyo-Marakwet', 'Nandi', 'Baringo', 'Uasin Gishu',
        'Kericho', 'Bomet', 'Kakamega', 'Vihiga', 'Bungoma', 'Busia',
        'Siaya', 'Kisumu', 'Homa Bay', 'Migori', 'Kisii', 'Nyamira',
        'Narok', 'Taita Taveta', 'Kilifi', 'Tana River', 'Lamu', 'Kwale',
        'Makueni', 'Kitui', 'Mwingi', 'Nzoia',
    ];

    public function create()
    {
        return view('properties.create', [
            'propertyTypes' => $this->propertyTypes,
            'listingTypes'  => $this->listingTypes,
            'counties'      => $this->counties,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'type'         => 'required|in:' . implode(',', $this->propertyTypes),
            'listing_type' => 'required|in:' . implode(',', $this->listingTypes),
            'price'        => 'required|numeric|min:1',
            'county'       => 'required|string',
            'location'     => 'required|string',
            'bedrooms'     => 'nullable|integer|min:0|max:50',
            'bathrooms'    => 'nullable|integer|min:0|max:50',
            'area_sqft'    => 'nullable|numeric',
            'year_built'   => 'nullable|integer|min:1900|max:2100',
            'floors'       => 'nullable|integer|min:1',
            'price_period' => 'nullable|string',
            'constituency' => 'nullable|string',
            'images'       => 'nullable|array|max:10',
            'images.*'     => 'image|max:5120',
            'amenities'    => 'nullable|array',
        ]);

        $userId = session('user_id');
        $uploadedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store("properties/{$userId}", 'public');
                $uploadedImages[] = $path;
            }
        }

        $property = Property::create([
            'user_id'      => $userId,
            'title'        => $data['title'],
            'description'  => $data['description'],
            'type'         => $data['type'],
            'listing_type' => $data['listing_type'],
            'price'        => $data['price'],
            'county'       => $data['county'],
            'location'     => $data['location'],
            'bedrooms'     => $data['bedrooms'] ?? null,
            'bathrooms'    => $data['bathrooms'] ?? null,
            'area_sqft'    => $data['area_sqft'] ?? null,
            'year_built'   => $data['year_built'] ?? null,
            'floors'       => $data['floors'] ?? null,
            'price_period' => $data['price_period'] ?? null,
            'constituency' => $data['constituency'] ?? null,
            'images'       => $uploadedImages,
            'amenities'    => $data['amenities'] ?? [],
            'status'       => 'active',
        ]);

        return redirect('/dashboard/' . $this->dashboardSlug())
            ->with('success', "Property \"{$property->title}\" listed successfully!");
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $this->authorizeOwner($property);

        return view('properties.edit', [
            'property'      => $property,
            'propertyTypes' => $this->propertyTypes,
            'listingTypes'  => $this->listingTypes,
            'counties'      => $this->counties,
        ]);
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $this->authorizeOwner($property);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'type'         => 'required|in:' . implode(',', $this->propertyTypes),
            'listing_type' => 'required|in:' . implode(',', $this->listingTypes),
            'price'        => 'required|numeric|min:1',
            'county'       => 'required|string',
            'location'     => 'required|string',
            'bedrooms'     => 'nullable|integer|min:0|max:50',
            'bathrooms'    => 'nullable|integer|min:0|max:50',
            'area_sqft'    => 'nullable|numeric',
            'year_built'   => 'nullable|integer|min:1900|max:2100',
            'floors'       => 'nullable|integer|min:1',
            'price_period' => 'nullable|string',
            'constituency' => 'nullable|string',
            'images'       => 'nullable|array|max:10',
            'images.*'     => 'image|max:5120',
            'amenities'    => 'nullable|array',
        ]);

        $userId = session('user_id');
        $existingImages = $property->images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store("properties/{$userId}", 'public');
                $existingImages[] = $path;
            }
        }

        $property->update([
            'title'        => $data['title'],
            'description'  => $data['description'],
            'type'         => $data['type'],
            'listing_type' => $data['listing_type'],
            'price'        => $data['price'],
            'county'       => $data['county'],
            'location'     => $data['location'],
            'bedrooms'     => $data['bedrooms'] ?? null,
            'bathrooms'    => $data['bathrooms'] ?? null,
            'area_sqft'    => $data['area_sqft'] ?? null,
            'year_built'   => $data['year_built'] ?? null,
            'floors'       => $data['floors'] ?? null,
            'price_period' => $data['price_period'] ?? null,
            'constituency' => $data['constituency'] ?? null,
            'images'       => $existingImages,
            'amenities'    => $data['amenities'] ?? [],
        ]);

        return redirect('/dashboard/' . $this->dashboardSlug())
            ->with('success', "Property \"{$property->title}\" updated successfully!");
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $this->authorizeOwner($property);

        $property->delete();

        return redirect('/dashboard/' . $this->dashboardSlug())
            ->with('success', 'Property deleted successfully.');
    }

    private function authorizeOwner(Property $property): void
    {
        $userId = session('user_id');
        $role   = session('role');

        if ($property->user_id !== $userId && $role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    private function dashboardSlug(): string
    {
        $role = session('role');
        $map = [
            'admin'              => 'admin',
            'landlord'           => 'landlord',
            'broker_licensed'    => 'broker',
            'broker_unlicensed'  => 'promoter',
            'tenant'             => 'tenant',
            'developer'          => 'developer',
            'auctioneer'         => 'auctioneer',
        ];
        return $map[$role] ?? 'tenant';
    }
}
