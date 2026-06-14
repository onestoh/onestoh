<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class AdminListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with(['user', 'category', 'primaryPhoto'])->withTrashed();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($s) => $s->where('title', 'like', "%{$q}%")->orWhere('make', 'like', "%{$q}%"));
        }
        if ($request->filled('status')) $query->where('status', $request->status);

        $listings = $query->latest()->paginate(25)->withQueryString();
        return view('admin.listings.index', compact('listings'));
    }

    public function show(Listing $listing)
    {
        $listing->load(['user', 'category', 'photos', 'bookings' => fn($q) => $q->latest()->limit(5)]);
        return view('admin.listings.show', compact('listing'));
    }

    public function approve(Listing $listing)
    {
        $listing->update(['status' => 'active']);
        return back()->with('success', 'Listing approved and published.');
    }

    public function feature(Listing $listing)
    {
        $listing->update(['is_featured' => !$listing->is_featured]);
        return back()->with('success', $listing->is_featured ? 'Listing featured.' : 'Listing unfeatured.');
    }

    public function destroy(Listing $listing)
    {
        $listing->delete();
        return back()->with('success', 'Listing removed.');
    }
}
