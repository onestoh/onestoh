<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Property;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProperties = Property::featured()->active()->with('owner')->latest()->take(8)->get();

        $liveAuctions = Auction::with(['property', 'auctioneer'])
            ->whereIn('status', ['live', 'upcoming'])
            ->orderBy('starts_at')
            ->take(4)
            ->get();

        $propertyCounts = Property::active()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return view('welcome', compact('featuredProperties', 'liveAuctions', 'propertyCounts'));
    }

    public function financing()
    {
        return view('pages.financing');
    }

    public function verification()
    {
        return view('pages.verification');
    }

    public function about()
    {
        return view('pages.about');
    }
}
