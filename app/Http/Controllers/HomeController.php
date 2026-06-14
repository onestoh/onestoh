<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\Listing;
use App\Models\Booking;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::withCount('listings')->orderBy('name')->take(6)->get();

        $featuredListings = collect();
        try {
            $featuredListings = Listing::where('status', 'active')
                ->where('is_featured', true)
                ->with(['photos', 'category', 'user'])
                ->take(6)
                ->get();
        } catch (\Exception $e) {
            // Table may not have is_featured column yet
        }

        $stats = [
            'total_listings' => Listing::where('status', 'active')->count(),
            'total_yards' => User::where('role', 'yard_owner')->where('status', 'verified')->count(),
            'total_bookings' => Booking::where('status', 'completed')->count(),
        ];

        return view('welcome', compact('categories', 'featuredListings', 'stats'));
    }
}
