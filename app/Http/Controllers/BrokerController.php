<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BrokerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'total_earned'    => DB::table('broker_commissions')->where('broker_id', $user->id)->where('status', 'paid')->sum('amount'),
            'pending_amount'  => DB::table('broker_commissions')->where('broker_id', $user->id)->where('status', 'pending')->sum('amount'),
            'total_deals'     => DB::table('broker_commissions')->where('broker_id', $user->id)->count(),
            'wallet_balance'  => $user->wallet?->balance ?? 0,
            'referral_code'   => $user->referral_code,
        ];

        $recentCommissions = DB::table('broker_commissions')
            ->where('broker_id', $user->id)
            ->join('bookings', 'broker_commissions.booking_id', '=', 'bookings.id')
            ->join('listings', 'bookings.listing_id', '=', 'listings.id')
            ->select('broker_commissions.*', 'bookings.booking_ref', 'listings.title as listing_title')
            ->orderByDesc('broker_commissions.created_at')
            ->limit(10)
            ->get();

        return view('broker.dashboard', compact('stats', 'recentCommissions', 'user'));
    }

    public function commissions()
    {
        $commissions = DB::table('broker_commissions')
            ->where('broker_id', auth()->id())
            ->join('bookings', 'broker_commissions.booking_id', '=', 'bookings.id')
            ->join('listings', 'bookings.listing_id', '=', 'listings.id')
            ->select('broker_commissions.*', 'bookings.booking_ref', 'listings.title as listing_title')
            ->orderByDesc('broker_commissions.created_at')
            ->paginate(20);

        return view('broker.commissions', compact('commissions'));
    }
}
