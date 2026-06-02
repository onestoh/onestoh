<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Booking;
use App\Models\EscrowTransaction;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Referral;
use App\Models\RentPayment;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function currentUser(): ?User
    {
        $id = session('user_id');
        return $id ? User::find($id) : null;
    }

    public function index()
    {
        $role = session('role');
        $routes = [
            'admin'            => 'dashboard.admin',
            'landlord'         => 'dashboard.landlord',
            'broker_licensed'  => 'dashboard.broker',
            'broker_unlicensed'=> 'dashboard.promoter',
            'tenant'           => 'dashboard.tenant',
            'developer'        => 'dashboard.developer',
            'valuer'           => 'dashboard.valuer',
            'surveyor'         => 'dashboard.surveyor',
            'auctioneer'       => 'dashboard.auctioneer',
            'investor'         => 'dashboard.investor',
            'corporate'        => 'dashboard.corporate',
            'property_manager' => 'dashboard.property-manager',
            'finance'          => 'dashboard.finance',
        ];
        $route = $routes[$role] ?? 'dashboard.tenant';
        return redirect()->route($route);
    }

    public function admin()
    {
        $userCountsByRole = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $propertyCounts = [
            'total'   => Property::count(),
            'active'  => Property::where('status', 'active')->count(),
            'pending' => Property::where('status', 'pending')->count(),
            'sold'    => Property::where('status', 'sold')->count(),
        ];

        $recentTransactions = EscrowTransaction::with(['property', 'buyer', 'seller'])
            ->latest()
            ->take(10)
            ->get();

        $recentVerifications = Verification::with('user')
            ->latest()
            ->take(10)
            ->get();

        $totalUsers = User::count();

        return view('dashboard.admin.index', compact(
            'userCountsByRole', 'propertyCounts', 'recentTransactions', 'recentVerifications', 'totalUsers'
        ));
    }

    public function landlord()
    {
        $userId = session('user_id');

        $properties = Property::where('user_id', $userId)->withCount('leases')->latest()->get();

        $leases = Lease::where('landlord_id', $userId)
            ->with(['tenant', 'property'])
            ->latest()
            ->get();

        $rentPaymentsByStatus = RentPayment::where('landlord_id', $userId)
            ->selectRaw('status, count(*) as count, sum(amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $maintenanceRequests = MaintenanceRequest::whereHas('property', fn($q) => $q->where('user_id', $userId))
            ->with('property')
            ->latest()
            ->take(10)
            ->get();

        // Bookings for this landlord's properties
        $propertyIds = $properties->pluck('id')->toArray();
        $activeBookingsCount = Booking::whereIn('property_id', $propertyIds)
            ->whereIn('status', ['pending', 'confirmed', 'paid', 'checked_in'])
            ->count();
        $upcomingBookings = Booking::with(['property', 'guest'])
            ->whereIn('property_id', $propertyIds)
            ->whereIn('status', ['confirmed', 'paid', 'checked_in'])
            ->where('check_in', '>=', now()->toDateString())
            ->orderBy('check_in')
            ->take(10)
            ->get();

        return view('dashboard.landlord.index', compact(
            'properties', 'leases', 'rentPaymentsByStatus', 'maintenanceRequests',
            'activeBookingsCount', 'upcomingBookings'
        ));
    }

    public function broker()
    {
        $userId = session('user_id');

        $properties = Property::where('user_id', $userId)->latest()->get();

        $referrals = Referral::where('referrer_id', $userId)
            ->with('referredUser')
            ->latest()
            ->get();

        $totalEarned = $referrals->sum('total_earned');
        $totalConversions = $referrals->sum('conversion_count');

        return view('dashboard.broker-licensed.index', compact(
            'properties', 'referrals', 'totalEarned', 'totalConversions'
        ));
    }

    public function promoter()
    {
        $userId = session('user_id');

        $referrals = Referral::where('referrer_id', $userId)
            ->with('referredUser')
            ->latest()
            ->get();

        $totalEarned = $referrals->sum('total_earned');
        $totalClicks = $referrals->sum('click_count');

        return view('dashboard.broker-unlicensed.index', compact(
            'referrals', 'totalEarned', 'totalClicks'
        ));
    }

    public function tenant()
    {
        $userId = session('user_id');

        $activeLease = Lease::where('tenant_id', $userId)
            ->where('status', 'active')
            ->with('property', 'landlord')
            ->first();

        $upcomingPayment = null;
        if ($activeLease) {
            $upcomingPayment = RentPayment::where('lease_id', $activeLease->id)
                ->whereIn('status', ['pending', 'overdue'])
                ->orderBy('due_date')
                ->first();
        }

        $paymentHistory = RentPayment::where('tenant_id', $userId)
            ->latest('paid_at')
            ->take(12)
            ->get();

        $maintenanceRequests = MaintenanceRequest::where('tenant_id', $userId)
            ->with('property')
            ->latest()
            ->get();

        $myBookings = Booking::where('guest_id', $userId)
            ->with('property')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.tenant.index', compact(
            'activeLease', 'upcomingPayment', 'paymentHistory', 'maintenanceRequests', 'myBookings'
        ));
    }

    public function developer()
    {
        $userId = session('user_id');

        $projects = \App\Models\DeveloperProject::where('developer_id', $userId)->latest()->get();
        $properties = Property::where('user_id', $userId)->latest()->get();

        return view('dashboard.developer.index', compact('projects', 'properties'));
    }

    public function valuer()
    {
        $userId = session('user_id');

        $valuations = \App\Models\Valuation::where('valuer_id', $userId)
            ->with(['property', 'client'])
            ->latest()
            ->get();

        $pendingCount   = $valuations->where('status', 'pending')->count();
        $completedCount = $valuations->where('status', 'completed')->count();
        $totalFees      = $valuations->sum('fee');

        return view('dashboard.valuer.index', compact(
            'valuations', 'pendingCount', 'completedCount', 'totalFees'
        ));
    }

    public function surveyor()
    {
        $userId = session('user_id');

        $surveys = \App\Models\Survey::where('surveyor_id', $userId)
            ->with(['property', 'client'])
            ->latest()
            ->get();

        $pendingCount   = $surveys->where('status', 'pending')->count();
        $completedCount = $surveys->where('status', 'completed')->count();
        $totalFees      = $surveys->sum('fee');

        return view('dashboard.surveyor.index', compact(
            'surveys', 'pendingCount', 'completedCount', 'totalFees'
        ));
    }

    public function auctioneer()
    {
        $userId = session('user_id');

        $auctions = Auction::where('auctioneer_id', $userId)
            ->with(['property', 'bids'])
            ->latest()
            ->get();

        $liveCount     = $auctions->where('status', 'live')->count();
        $upcomingCount = $auctions->where('status', 'upcoming')->count();
        $endedCount    = $auctions->where('status', 'ended')->count();

        return view('dashboard.auctioneer.index', compact(
            'auctions', 'liveCount', 'upcomingCount', 'endedCount'
        ));
    }

    public function investor()
    {
        $userId = session('user_id');

        $savedProperties = Property::whereHas('saves', fn($q) => $q->where('user_id', $userId))
            ->with('owner')
            ->latest()
            ->get();

        $escrowTransactions = EscrowTransaction::where('buyer_id', $userId)
            ->with(['property', 'seller'])
            ->latest()
            ->get();

        return view('dashboard.investor.index', compact('savedProperties', 'escrowTransactions'));
    }

    public function corporate()
    {
        $userId = session('user_id');

        $properties = Property::where('user_id', $userId)->latest()->get();
        $leases = Lease::where('tenant_id', $userId)->with(['property', 'landlord'])->latest()->get();

        return view('dashboard.corporate.index', compact('properties', 'leases'));
    }

    public function propertyManager()
    {
        $userId = session('user_id');

        $managedProperties = Property::where('user_id', $userId)->withCount('maintenanceRequests')->latest()->get();

        $openMaintenanceRequests = MaintenanceRequest::whereHas('property', fn($q) => $q->where('user_id', $userId))
            ->where('status', 'open')
            ->with(['property', 'tenant'])
            ->latest()
            ->get();

        return view('dashboard.property-manager.index', compact(
            'managedProperties', 'openMaintenanceRequests'
        ));
    }

    public function finance()
    {
        $totalRentCollected = RentPayment::where('status', 'paid')->sum('amount');
        $pendingRent        = RentPayment::where('status', 'pending')->sum('amount');
        $overdueRent        = RentPayment::where('status', 'overdue')->sum('amount');

        $escrowHeld     = EscrowTransaction::where('status', 'held')->sum('amount');
        $escrowReleased = EscrowTransaction::where('status', 'released')->sum('amount');

        $recentPayments = RentPayment::with(['tenant', 'landlord', 'lease.property'])
            ->latest()
            ->take(20)
            ->get();

        return view('dashboard.finance.index', compact(
            'totalRentCollected', 'pendingRent', 'overdueRent',
            'escrowHeld', 'escrowReleased', 'recentPayments'
        ));
    }

    public function messages()
    {
        $userId = session('user_id');
        $messages = \App\Models\Message::where('recipient_id', $userId)
            ->with('sender')
            ->latest()
            ->get();

        return view('dashboard.shared.messages', compact('messages'));
    }

    public function notifications()
    {
        $userId = session('user_id');
        $notifications = \App\Models\NotificationLog::where('user_id', $userId)
            ->latest()
            ->get();

        return view('dashboard.shared.notifications', compact('notifications'));
    }

    public function settings()
    {
        return view('dashboard.shared.settings');
    }

    public function profile()
    {
        $userId = session('user_id');
        $user = User::find($userId);
        return view('dashboard.shared.profile', compact('user'));
    }

    public function verification()
    {
        $userId = session('user_id');
        $verification = Verification::with('documents')->where('user_id', $userId)->first();
        return view('dashboard.shared.verification', compact('verification'));
    }

    public function analytics()
    {
        $userId     = session('user_id');
        $properties = Property::where('user_id', $userId)->get();
        $propertyIds = $properties->pluck('id');

        // Monthly revenue (rent + bookings combined) last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month  = now()->subMonths($i);
            $rent   = RentPayment::where('landlord_id', $userId)->where('status', 'paid')
                ->whereYear('paid_at', $month->year)->whereMonth('paid_at', $month->month)->sum('amount');
            $book   = Booking::whereIn('property_id', $propertyIds)->whereIn('status', ['confirmed','checked_out'])
                ->whereYear('paid_at', $month->year)->whereMonth('paid_at', $month->month)->sum('total_price');
            $monthlyRevenue[] = ['month' => $month->format('M y'), 'total' => $rent + $book];
        }

        // Property performance table
        $propertyPerformance = [];
        foreach ($properties->take(10) as $prop) {
            $bookings = Booking::where('property_id', $prop->id)->whereIn('status', ['confirmed','checked_out'])->get();
            $propertyPerformance[] = [
                'title'        => $prop->title,
                'listing_type' => $prop->listing_type,
                'bookings'     => $bookings->count(),
                'revenue'      => $bookings->sum('total_price'),
                'avg_nights'   => $bookings->avg('nights') ?? 0,
                'status'       => $prop->status ?? 'available',
            ];
        }
        usort($propertyPerformance, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        $totalRevenue   = collect($monthlyRevenue)->sum('total');
        $bookings30d    = Booking::whereIn('property_id', $propertyIds)->where('created_at', '>=', now()->subDays(30))->count();
        $activeListings = $properties->where('status', 'available')->count();
        $totalBookings  = Booking::whereIn('property_id', $propertyIds)->whereIn('status', ['confirmed','checked_out'])->count();
        $totalNights    = Booking::whereIn('property_id', $propertyIds)->whereIn('status', ['confirmed','checked_out'])->sum('nights');
        $capacityNights = $properties->count() * 30;
        $occupancyRate  = $capacityNights > 0 ? min(100, round($totalNights / $capacityNights * 100)) : 0;

        $analytics = [
            'monthly_revenue'      => $monthlyRevenue,
            'property_performance' => $propertyPerformance,
            'total_revenue'        => $totalRevenue,
            'active_listings'      => $activeListings,
            'bookings_30d'         => $bookings30d,
            'occupancy_rate'       => $occupancyRate,
        ];

        return view('dashboard.shared.analytics', compact('analytics'));
    }
}
