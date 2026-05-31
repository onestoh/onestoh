<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\NewBidPlaced;
use App\Models\Auction;
use App\Models\Bid;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuctionApiController extends Controller
{
    public function index()
    {
        $auctions = Auction::with('property:id,title,images,county,location')
            ->latest()
            ->get()
            ->groupBy('status');

        return response()->json([
            'success' => true,
            'data'    => $auctions,
        ]);
    }

    public function show($id)
    {
        $auction = Auction::with([
            'property:id,title,images,county,location,price',
            'auctioneer:id,name',
        ])->findOrFail($id);

        $bids = Bid::where('auction_id', $id)
            ->with('bidder:id,name')
            ->latest()
            ->take(10)
            ->get();

        $bidderCount = Bid::where('auction_id', $id)
            ->distinct('bidder_id')
            ->count('bidder_id');

        return response()->json([
            'success' => true,
            'data'    => [
                'auction'      => $auction,
                'recent_bids'  => $bids,
                'bidder_count' => $bidderCount,
            ],
        ]);
    }

    public function bid(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $auction = Auction::findOrFail($id);

        if ($auction->status !== 'live') {
            return response()->json([
                'success' => false,
                'message' => 'This auction is not currently live.',
            ], 422);
        }

        $minBid = ($auction->current_bid ?? $auction->starting_bid) + ($auction->bid_increment ?? 1000);
        if ($request->amount < $minBid) {
            return response()->json([
                'success' => false,
                'message' => 'Bid must be at least KES ' . number_format($minBid, 0) . '.',
            ], 422);
        }

        $user = $request->user();

        $previousTopBid = Bid::where('auction_id', $auction->id)
            ->where('is_winning', true)
            ->with('bidder')
            ->first();

        Bid::where('auction_id', $auction->id)
            ->where('is_winning', true)
            ->update(['is_winning' => false]);

        $bid = Bid::create([
            'auction_id' => $auction->id,
            'bidder_id'  => $user->id,
            'amount'     => $request->amount,
            'is_winning' => true,
        ]);

        $auction->update(['current_bid' => $request->amount]);

        if ($previousTopBid && $previousTopBid->bidder_id !== $user->id) {
            try {
                $prevBidder = $previousTopBid->bidder;
                if ($prevBidder?->email) {
                    Mail::to($prevBidder->email)->queue(new NewBidPlaced($bid, $auction));
                }
                NotificationService::send(
                    $previousTopBid->bidder_id,
                    'You\'ve Been Outbid',
                    'A new bid of KES ' . number_format($request->amount, 0) . ' was placed.',
                    'auction',
                    '/auctions/' . $auction->id
                );
            } catch (\Throwable $e) {
                Log::warning('API NewBidPlaced mail failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'bid_id'      => $bid->id,
                'new_bid'     => $request->amount,
                'new_bid_fmt' => 'KES ' . number_format($request->amount, 0),
            ],
        ]);
    }
}
