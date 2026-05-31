<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['owner:id,name,is_verified'])
            ->where('status', 'active');

        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }
        if ($request->filled('county')) {
            $query->where('county', $request->county);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $properties = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $properties->items(),
            'meta'    => [
                'page'         => $properties->currentPage(),
                'per_page'     => $properties->perPage(),
                'total'        => $properties->total(),
                'last_page'    => $properties->lastPage(),
            ],
        ]);
    }

    public function show($id)
    {
        $property = Property::with(['owner:id,name,email,phone,is_verified', 'documents'])
            ->where('status', 'active')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $property,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'price'        => 'required|numeric|min:0',
            'property_type'=> 'required|string',
            'listing_type' => 'required|in:sale,rent,auction',
            'county'       => 'required|string',
            'location'     => 'required|string',
        ]);

        $user = $request->user();

        $property = Property::create([
            'user_id'       => $user->id,
            'title'         => $request->title,
            'description'   => $request->description,
            'price'         => $request->price,
            'property_type' => $request->property_type,
            'listing_type'  => $request->listing_type,
            'county'        => $request->county,
            'location'      => $request->location,
            'bedrooms'      => $request->bedrooms,
            'bathrooms'     => $request->bathrooms,
            'area_sqft'     => $request->area_sqft,
            'amenities'     => $request->amenities ?? [],
            'status'        => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'data'    => $property,
        ], 201);
    }
}
