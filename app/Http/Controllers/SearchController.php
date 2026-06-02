<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::where('status', 'active');

        if ($request->q) {
            $q = $request->q;
            $query->where(fn($s) => $s->where('title','like',"%{$q}%")->orWhere('location','like',"%{$q}%")->orWhere('county','like',"%{$q}%")->orWhere('description','like',"%{$q}%"));
        }
        if ($request->type) $query->where('type', $request->type);
        if ($request->listing_type) $query->whereIn('listing_type', (array)$request->listing_type);
        if ($request->county) $query->where('county', $request->county);
        if ($request->bedrooms) $query->where('bedrooms', '>=', $request->bedrooms);
        if ($request->min_price) $query->where('price', '>=', $request->min_price);
        if ($request->max_price) $query->where('price', '<=', $request->max_price);
        if ($request->verified_only) $query->whereHas('owner', fn($u) => $u->where('is_verified', true));
        if ($request->featured_only) $query->where('is_featured', true);

        $sortMap = ['price_asc'=>'price', 'price_desc'=>'price', 'newest'=>'created_at', 'views'=>'view_count'];
        $dirMap  = ['price_asc'=>'asc', 'price_desc'=>'desc', 'newest'=>'desc', 'views'=>'desc'];
        $sort = $request->get('sort', 'newest');
        $query->orderBy($sortMap[$sort] ?? 'created_at', $dirMap[$sort] ?? 'desc');

        $properties = $query->with('owner')->paginate(12)->appends($request->query());

        $searchQuery = $request->input('q', '');
        $counties = Property::where('status', 'active')->distinct()->pluck('county')->filter()->sort()->values();

        return view('search.results', [
            'properties' => $properties,
            'query'      => $searchQuery,
            'counties'   => $counties,
        ]);
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
