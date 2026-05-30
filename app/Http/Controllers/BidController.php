<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function place(Request $request, $id)
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
                'message' => "Bid must be at least KES " . number_format($minBid, 0) . ".",
            ], 422);
        }

        $userId = session('user_id');

        // Mark all previous winning bids as non-winning
        Bid::where('auction_id', $auction->id)
            ->where('is_winning', true)
            ->update(['is_winning' => false]);

        // Create new bid
        $bid = Bid::create([
            'auction_id' => $auction->id,
            'bidder_id'  => $userId,
            'amount'     => $request->amount,
            'is_winning' => true,
        ]);

        // Update auction current bid
        $auction->update(['current_bid' => $request->amount]);

        return response()->json([
            'success'  => true,
            'new_bid'  => number_format($request->amount, 0),
            'bidder'   => session('user_name'),
            'bid_id'   => $bid->id,
        ]);
    }
}
