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
        $categories = AssetCategory::where('is_active', true)->orderBy('sort_order')->get();
        $featuredListings = Listing::active()->featured()->with(['photos','yard','category'])->limit(8)->get();
        $latestListings = Listing::active()->with(['photos','yard','category'])->latest()->limit(12)->get();

        $stats = [
            'total_listings' => Listing::active()->count(),
            'total_yards' => User::where('role', 'yard_owner')->where('status', 'verified')->count(),
            'total_bookings' => Booking::where('status', 'completed')->count(),
        ];

        return view('home', compact('categories', 'featuredListings', 'latestListings', 'stats'));
    }
}
