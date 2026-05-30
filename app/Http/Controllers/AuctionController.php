<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index()
    {
        $liveAuctions = Auction::with(['property', 'auctioneer'])
            ->where('status', 'live')
            ->orderBy('ends_at')
            ->get();

        $upcomingAuctions = Auction::with(['property', 'auctioneer'])
            ->where('status', 'upcoming')
            ->orderBy('starts_at')
            ->get();

        $endedAuctions = Auction::with(['property', 'auctioneer', 'winner'])
            ->where('status', 'ended')
            ->latest()
            ->take(10)
            ->get();

        return view('auctions.index', compact('liveAuctions', 'upcomingAuctions', 'endedAuctions'));
    }

    public function show($id)
    {
        $auction = Auction::with(['property.owner', 'auctioneer', 'winner'])->findOrFail($id);

        $recentBids = $auction->bids()
            ->with('bidder')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $bidderCount = $auction->bids()->distinct('bidder_id')->count('bidder_id');

        return view('auctions.show', compact('auction', 'recentBids', 'bidderCount'));
    }
}
