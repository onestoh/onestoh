<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\Listing;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with(['category', 'primaryPhoto', 'user', 'yard'])
            ->active();

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($sub) use ($kw) {
                $sub->where('title', 'like', "%{$kw}%")
                    ->orWhere('description', 'like', "%{$kw}%")
                    ->orWhere('make', 'like', "%{$kw}%")
                    ->orWhere('model', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->whereIn('asset_category_id', (array) $request->category_id);
        }

        if ($request->filled('county')) {
            $query->where(function ($sub) use ($request) {
                $sub->whereHas('yard', fn($y) => $y->where('county', $request->county))
                    ->orWhere('county', $request->county);
            });
        }

        if ($request->filled('listing_mode') && $request->listing_mode !== 'both') {
            if ($request->listing_mode === 'rental') {
                $query->forRent();
            } elseif ($request->listing_mode === 'sale') {
                $query->forSale();
            }
        }

        match($request->get('sort_by', 'newest')) {
            'price_asc'  => $query->orderBy('daily_rate', 'asc'),
            'price_desc' => $query->orderBy('daily_rate', 'desc'),
            'rating'     => $query->orderBy('rating_avg', 'desc'),
            default      => $query->latest(),
        };

        $listings = $query->paginate(12)->withQueryString();
        $categories = AssetCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('marketplace.index', compact('listings', 'categories'));
    }

    public function show(string $slug)
    {
        $listing = Listing::with(['user', 'yard', 'category', 'photos', 'reviews.user'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $reviews = $listing->reviews()->with('user')->where('status', 'approved')->latest()->get();

        return view('marketplace.show', compact('listing', 'reviews'));
    }
}
