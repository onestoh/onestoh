<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\KycDocument;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'     => User::count(),
            'total_listings'  => Listing::count(),
            'total_bookings'  => Booking::count(),
            'revenue_total'   => Booking::where('status', 'completed')->sum('total_amount'),
            'pending_kyc'     => KycDocument::where('status', 'pending')->distinct('user_id')->count('user_id'),
            'open_disputes'   => DB::table('disputes')->where('status', 'open')->count(),
            'pending_payouts' => DB::table('payout_requests')->where('status', 'pending')->count(),
            'active_listings' => Listing::where('status', 'active')->count(),
        ];

        $recentBookings = Booking::with(['listing', 'client'])->latest()->limit(8)->get();
        $recentUsers    = User::latest()->limit(6)->get();
        $monthlyRevenue = Booking::where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentUsers', 'monthlyRevenue'));
    }
}
