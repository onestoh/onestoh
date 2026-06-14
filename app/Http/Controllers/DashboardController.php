<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user()->load('wallet', 'kycDocuments');
        $role = $user->role;

        $recentBookings = Booking::with('listing')
            ->where(function ($q) use ($user) {
                $q->where('client_id', $user->id)
                  ->orWhere('operator_id', $user->id);
            })
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'wallet_balance'  => $user->wallet?->balance ?? 0,
            'active_bookings' => Booking::where('client_id', $user->id)->whereIn('status', ['confirmed', 'active'])->count(),
            'kyc_status'      => $user->status,
            'kyc_doc_count'   => $user->kycDocuments->count(),
        ];

        if (in_array($role, ['yard_owner', 'individual_owner'])) {
            $stats['total_listings']     = Listing::where('user_id', $user->id)->count();
            $stats['pending_requests']   = Booking::whereHas('listing', fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'pending_payment')
                ->count();
            $stats['revenue_this_month'] = Booking::whereHas('listing', fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount');
        }

        if ($role === 'broker') {
            $stats['total_commissions']   = DB::table('broker_commissions')
                ->where('broker_id', $user->id)
                ->where('status', 'paid')
                ->sum('amount');
            $stats['pending_commissions'] = DB::table('broker_commissions')
                ->where('broker_id', $user->id)
                ->where('status', 'pending')
                ->sum('amount');
            $stats['active_deals'] = Booking::where('broker_id', $user->id)
                ->whereIn('status', ['confirmed', 'active'])
                ->count();
        }

        if ($role === 'client') {
            $stats['total_bookings'] = Booking::where('client_id', $user->id)->count();
            $stats['next_booking']   = Booking::with('listing')
                ->where('client_id', $user->id)
                ->whereIn('status', ['confirmed', 'active'])
                ->where('start_datetime', '>=', now())
                ->orderBy('start_datetime')
                ->first();
        }

        return view('dashboard.index', compact('user', 'stats', 'recentBookings', 'role'));
    }
}
