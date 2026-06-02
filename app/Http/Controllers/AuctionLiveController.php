<?php
namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AuctionLiveController extends Controller
{
    // GET /auctions/{id}/live-data — polled every 3s by the auction show page
    public function liveData($id)
    {
        $auction = Auction::with(['property', 'bids' => fn($q) => $q->with('bidder')->latest()->take(10)])->findOrFail($id);

        return response()->json([
            'current_bid'  => $auction->current_bid,
            'bid_count'    => $auction->bids->count(),
            'status'       => $auction->status,
            'ends_at'      => $auction->ends_at,
            'time_left'    => now()->diffInSeconds($auction->ends_at, false),
            'recent_bids'  => $auction->bids->map(fn($b) => [
                'bidder'    => optional($b->bidder)->name ?? 'Anonymous',
                'amount'    => $b->amount,
                'formatted' => 'KES ' . number_format($b->amount),
                'time'      => $b->created_at->diffForHumans(),
                'is_winning'=> $b->is_winning,
            ]),
            'winner' => $auction->status === 'ended' ? optional(User::find($auction->winner_id))->name : null,
        ]);
    }

    // POST /auctions/{id}/live-bid — place a bid (auth required)
    public function placeBid(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        $userId = session('user_id');
        if (!$userId) return response()->json(['success' => false, 'message' => 'Please login to bid'], 401);

        $auction = Auction::with('bids')->findOrFail($id);

        if ($auction->status !== 'live') {
            return response()->json(['success' => false, 'message' => 'Auction is not live'], 422);
        }

        $minBid = ($auction->current_bid ?? $auction->starting_bid) + $auction->bid_increment;

        if ($request->amount < $minBid) {
            return response()->json([
                'success' => false,
                'message' => 'Bid must be at least KES ' . number_format($minBid),
                'min_bid' => $minBid,
            ], 422);
        }

        // Mark old winning bid as not winning
        Bid::where('auction_id', $id)->where('is_winning', true)->update(['is_winning' => false]);

        // Create new bid
        $bid = Bid::create([
            'auction_id' => $id,
            'bidder_id'  => $userId,
            'amount'     => $request->amount,
            'is_winning' => true,
        ]);

        $auction->update(['current_bid' => $request->amount]);

        // Notify all previous bidders they've been outbid
        $previousBidders = Bid::where('auction_id', $id)
            ->where('bidder_id', '!=', $userId)
            ->distinct('bidder_id')
            ->pluck('bidder_id');

        $bidderName = optional(\App\Models\User::find($userId))->name ?? 'Someone';
        $propTitle  = optional($auction->property)->title ?? 'the property';

        foreach ($previousBidders as $bidderId) {
            NotificationService::send($bidderId,
                'You\'ve been outbid!',
                "{$bidderName} placed a bid of KES " . number_format($request->amount) . " on {$propTitle}. Place a higher bid to stay in the running.",
                'auction',
                '/auctions/' . $id
            );
        }

        // Notify auctioneer
        NotificationService::send($auction->auctioneer_id,
            'New Bid Placed',
            "KES " . number_format($request->amount) . " bid on {$propTitle} by {$bidderName}",
            'auction',
            '/dashboard/auctioneer'
        );

        return response()->json([
            'success'     => true,
            'new_bid'     => $request->amount,
            'formatted'   => 'KES ' . number_format($request->amount),
            'next_min'    => $request->amount + $auction->bid_increment,
            'bid_count'   => Bid::where('auction_id', $id)->count(),
        ]);
    }

    // POST /auctions/{id}/end — end an auction (auctioneer only)
    public function endAuction(Request $request, $id)
    {
        $userId  = session('user_id');
        $auction = Auction::with('bids')->findOrFail($id);

        $winningBid = $auction->bids()->where('is_winning', true)->first();

        $auction->update([
            'status'    => 'ended',
            'winner_id' => $winningBid?->bidder_id,
        ]);

        if ($winningBid) {
            NotificationService::send($winningBid->bidder_id, '🎉 You Won the Auction!',
                "Congratulations! You won the auction for " . optional($auction->property)->title . " with a bid of KES " . number_format($winningBid->amount) . ". The auctioneer will contact you within 24 hours.",
                'auction', '/auctions/' . $id);

            NotificationService::send($auction->auctioneer_id, 'Auction Ended',
                optional($auction->property)->title . " auction ended. Winner: " . optional($winningBid->bidder)->name . " — KES " . number_format($winningBid->amount),
                'auction', '/dashboard/auctioneer');
        }

        return response()->json(['success' => true, 'winner' => optional($winningBid?->bidder)->name]);
    }
}
