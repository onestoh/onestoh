<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::active()->with('owner');

        if ($request->type) {
            $query->where('listing_type', $request->type);
        }

        if ($request->property_type) {
            $query->where('type', $request->property_type);
        }

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->bedrooms) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->county) {
            $query->where('county', $request->county);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $properties = $query->latest()->paginate(12)->withQueryString();

        $counties = Property::active()->distinct()->orderBy('county')->pluck('county');

        return view('marketplace.index', compact('properties', 'counties'));
    }

    public function compare(Request $request)
    {
        $ids = array_slice(explode(',', $request->get('ids', '')), 0, 3);
        $ids = array_filter($ids, 'is_numeric');
        $properties = \App\Models\Property::whereIn('id', $ids)->get();
        return view('marketplace.compare', compact('properties'));
    }

    public function show($id)
    {
        $property = Property::with(['owner', 'documents', 'inspections'])->findOrFail($id);

        $property->increment('view_count');

        $relatedProperties = Property::active()
            ->where('type', $property->type)
            ->where('id', '!=', $property->id)
            ->take(4)
            ->get();

        return view('marketplace.show', compact('property', 'relatedProperties'));
    }
}
