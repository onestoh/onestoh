<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAnalyticsCache;
use App\Models\Booking;
use App\Models\PlatformAnalyticsSnapshot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class AnalyticsService
{
    /**
     * Platform-wide snapshot for admin dashboard.
     */
    public function getPlatformSnapshot(): array
    {
        $now           = Carbon::now();
        $startOfMonth  = $now->copy()->startOfMonth();
        $last30        = $now->copy()->subDays(30);

        $totalBookings = Booking::where('created_at', '>=', $last30)->count();
        $gmv           = Booking::where('status', 'completed')->where('created_at', '>=', $last30)->sum('total_price');
        $activeUsers   = User::where('last_login_at', '>=', $last30)->count();

        $started    = Booking::where('created_at', '>=', $last30)->count();
        $completed  = Booking::where('status', 'completed')->where('created_at', '>=', $last30)->count();
        $convRate   = $started > 0 ? round($completed / $started * 100, 2) : 0;

        $topCategories = Asset::join('bookings', 'assets.id', '=', 'bookings.asset_id')
            ->where('bookings.created_at', '>=', $last30)
            ->select('assets.category', DB::raw('COUNT(*) as total'))
            ->groupBy('assets.category')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'category')
            ->toArray();

        return [
            'total_bookings_30d'  => $totalBookings,
            'gmv_30d'             => round((float) $gmv, 2),
            'active_users_30d'    => $activeUsers,
            'conversion_rate_pct' => $convRate,
            'top_categories'      => $topCategories,
        ];
    }

    /**
     * Per-owner analytics with period support.
     */
    public function getOwnerAnalytics(User $owner, string $period): array
    {
        [$from, $to] = $this->periodRange($period);
        $assetIds = Asset::where('owner_id', $owner->id)->pluck('id');

        $revenue = Booking::whereIn('asset_id', $assetIds)
            ->where('status', 'completed')
            ->whereBetween('ends_at', [$from, $to])
            ->sum('total_price');

        $bookingCount = Booking::whereIn('asset_id', $assetIds)
            ->whereBetween('starts_at', [$from, $to])
            ->count();

        $topAssets = Booking::whereIn('asset_id', $assetIds)
            ->where('status', 'completed')
            ->whereBetween('ends_at', [$from, $to])
            ->select('asset_id', DB::raw('SUM(total_price) as revenue'))
            ->groupBy('asset_id')
            ->orderByDesc('revenue')
            ->limit(5)
            ->with('asset:id,name')
            ->get()
            ->map(fn ($b) => ['asset' => optional($b->asset)->name, 'revenue' => (float) $b->revenue])
            ->toArray();

        $brokerBreakdown = Booking::whereIn('asset_id', $assetIds)
            ->whereBetween('starts_at', [$from, $to])
            ->select('broker_id', DB::raw('COUNT(*) as bookings'))
            ->groupBy('broker_id')
            ->get()
            ->mapWithKeys(fn ($b) => [$b->broker_id ?? 'direct' => $b->bookings])
            ->toArray();

        return [
            'period'           => $period,
            'revenue'          => round((float) $revenue, 2),
            'booking_count'    => $bookingCount,
            'top_assets'       => $topAssets,
            'broker_breakdown' => $brokerBreakdown,
        ];
    }

    /**
     * Per-broker analytics.
     */
    public function getBrokerAnalytics(User $broker, string $period): array
    {
        [$from, $to] = $this->periodRange($period);

        $bookings = Booking::where('broker_id', $broker->id)
            ->whereBetween('starts_at', [$from, $to])
            ->get();

        $commissions = $bookings->where('status', 'completed')->sum('broker_commission');

        $bestListings = $bookings->groupBy('asset_id')
            ->map(fn ($g) => $g->count())
            ->sortDesc()
            ->take(5);

        return [
            'period'        => $period,
            'clicks'        => 0, // populated from Redis in a real implementation
            'conversions'   => $bookings->where('status', 'completed')->count(),
            'commissions'   => round((float) $commissions, 2),
            'best_listings' => $bestListings->toArray(),
        ];
    }

    /**
     * Client-level spend analytics.
     */
    public function getClientAnalytics(User $client): array
    {
        $bookings = Booking::where('client_id', $client->id)
            ->where('status', 'completed')
            ->with('asset')
            ->get();

        $totalSpend = $bookings->sum('total_price');

        $categoryBreakdown = $bookings
            ->groupBy(fn ($b) => optional($b->asset)->category ?? 'unknown')
            ->map(fn ($g) => $g->count())
            ->sortDesc()
            ->toArray();

        return [
            'total_spend'         => round((float) $totalSpend, 2),
            'booking_count'       => $bookings->count(),
            'favourite_categories'=> array_slice($categoryBreakdown, 0, 3, true),
        ];
    }

    /**
     * Conversion funnel using Redis counters.
     */
    public function getAdminFunnel(): array
    {
        $keys = [
            'funnel:listing_viewed',
            'funnel:calendar_checked',
            'funnel:booking_started',
            'funnel:payment_completed',
        ];

        $values = array_map(fn ($key) => (int) (Redis::get($key) ?? 0), $keys);

        return [
            'listings_viewed'    => $values[0],
            'calendar_checked'   => $values[1],
            'booking_started'    => $values[2],
            'payment_completed'  => $values[3],
            'overall_conversion' => $values[0] > 0
                ? round($values[3] / $values[0] * 100, 2)
                : 0,
        ];
    }

    /**
     * Bookings grouped by pickup county.
     */
    public function getGeographicBreakdown(): array
    {
        return Booking::select('pickup_county', DB::raw('COUNT(*) as total'))
            ->whereNotNull('pickup_county')
            ->groupBy('pickup_county')
            ->orderByDesc('total')
            ->pluck('total', 'pickup_county')
            ->toArray();
    }

    /**
     * Refresh all asset analytics caches (called from scheduled job).
     */
    public function refreshAllAssetCaches(): void
    {
        $fleetService = app(FleetService::class);
        Asset::chunk(100, function ($assets) use ($fleetService) {
            foreach ($assets as $asset) {
                $fleetService->refreshAnalyticsCache($asset);
            }
        });
    }

    private function periodRange(string $period): array
    {
        $now = Carbon::now();
        $from = match ($period) {
            '7d'  => $now->copy()->subDays(7),
            '30d' => $now->copy()->subDays(30),
            '90d' => $now->copy()->subDays(90),
            '1y'  => $now->copy()->subYear(),
            default => $now->copy()->subDays(30),
        };
        return [$from, $now];
    }
}
