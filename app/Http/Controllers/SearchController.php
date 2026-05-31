<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query    = $request->input('q', '');
        $type     = $request->input('type');
        $county   = $request->input('county');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $bedrooms = $request->input('bedrooms');

        $properties = Property::where('status', 'active')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('title', 'like', "%{$query}%")
                          ->orWhere('location', 'like', "%{$query}%")
                          ->orWhere('county', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%");
                });
            })
            ->when($type, fn($q) => $q->where('property_type', $type))
            ->when($county, fn($q) => $q->where('county', $county))
            ->when($minPrice, fn($q) => $q->where('price', '>=', $minPrice))
            ->when($maxPrice, fn($q) => $q->where('price', '<=', $maxPrice))
            ->when($bedrooms, fn($q) => $q->where('bedrooms', $bedrooms))
            ->with('owner:id,name,is_verified')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counties = Property::where('status', 'active')->distinct()->pluck('county')->filter()->sort()->values();

        return view('search.results', compact('properties', 'query', 'counties'));
    }

    public function suggestions(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $titles = Property::where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('location', 'like', "%{$q}%")
                      ->orWhere('county', 'like', "%{$q}%");
            })
            ->select('title', 'location', 'county')
            ->distinct()
            ->take(8)
            ->get()
            ->map(fn($p) => [
                'label'  => $p->title,
                'sub'    => $p->location . ', ' . $p->county,
            ]);

        return response()->json($titles);
    }
}
