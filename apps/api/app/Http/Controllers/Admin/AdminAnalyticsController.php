<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    /** GET /admin/analytics/platform */
    public function platformSnapshot(): JsonResponse
    {
        $snapshot = $this->analyticsService->getPlatformSnapshot();

        // Augment with user growth
        $snapshot['total_users']       = User::count();
        $snapshot['new_users_30d']     = User::where('created_at', '>=', now()->subDays(30))->count();
        $snapshot['total_assets']      = Asset::count();
        $snapshot['active_assets']     = Asset::where('status', 'available')->count();
        $snapshot['total_bookings']    = Booking::count();
        $snapshot['total_revenue']     = Booking::where('status', 'completed')->sum('total_price');

        return response()->json($snapshot);
    }

    /** GET /admin/analytics/funnel */
    public function conversionFunnel(): JsonResponse
    {
        return response()->json($this->analyticsService->getAdminFunnel());
    }

    /** GET /admin/analytics/geographic */
    public function geographicBreakdown(): JsonResponse
    {
        return response()->json($this->analyticsService->getGeographicBreakdown());
    }

    /** GET /admin/analytics/top-performers */
    public function topPerformers(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 10);

        $topOwners = Booking::join('assets', 'assets.id', '=', 'bookings.asset_id')
            ->where('bookings.status', 'completed')
            ->where('bookings.created_at', '>=', now()->subDays(30))
            ->select('assets.owner_id', DB::raw('SUM(bookings.total_price) as revenue'), DB::raw('COUNT(*) as bookings'))
            ->groupBy('assets.owner_id')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->with('owner:id,name,email')
            ->get();

        $topAssets = Booking::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('asset_id', DB::raw('SUM(total_price) as revenue'), DB::raw('COUNT(*) as bookings'))
            ->groupBy('asset_id')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->with('asset:id,name,category')
            ->get();

        return response()->json([
            'top_owners' => $topOwners,
            'top_assets' => $topAssets,
        ]);
    }

    /** GET /admin/analytics/growth */
    public function growthMetrics(): JsonResponse
    {
        $months = collect(range(0, 5))->map(function ($i) {
            $month = now()->subMonthsNoOverflow($i);
            return [
                'month'    => $month->format('Y-m'),
                'bookings' => Booking::whereBetween('created_at', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])->count(),
                'revenue'  => Booking::where('status', 'completed')->whereBetween('ends_at', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])->sum('total_price'),
                'new_users'=> User::whereBetween('created_at', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])->count(),
            ];
        })->values();

        return response()->json($months);
    }
}
