<?php
namespace App\Services;

use App\Models\{Asset, Booking, DataProduct, DataProductSubscription};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\{Cache, DB};
use Illuminate\Support\Str;

class DataMarketplaceService
{
    /**
     * Return all active data products.
     */
    public function getAvailableProducts(): Collection
    {
        return DataProduct::where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Create a subscription, generate API key, and record payment.
     */
    public function createSubscription(
        DataProduct $product,
        array $subscriberData,
        string $billingCycle
    ): DataProductSubscription {
        $amount = $billingCycle === 'annual' ? $product->price_annual : $product->price_monthly;
        $apiKey = Str::random(64);
        $periodEnds = $billingCycle === 'annual' ? now()->addYear() : now()->addMonth();

        return DataProductSubscription::create([
            'data_product_id'      => $product->id,
            'subscriber_name'      => $subscriberData['name'],
            'subscriber_email'     => $subscriberData['email'],
            'subscriber_type'      => $subscriberData['type'] ?? 'company',
            'billing_cycle'        => $billingCycle,
            'amount_paid'          => $amount,
            'api_key'              => $apiKey,
            'current_period_ends_at' => $periodEnds,
            'is_active'            => true,
        ]);
    }

    /**
     * Aggregate anonymised market report data.
     */
    public function generateMarketReport(string $category, string $period): array
    {
        $from = match ($period) {
            '7d'     => now()->subDays(7),
            '30d'    => now()->subDays(30),
            'quarter' => now()->subMonths(3),
            default  => now()->subDays(30),
        };

        $cacheKey = "market_report:{$category}:{$period}";
        return Cache::remember($cacheKey, 3600, function () use ($category, $from) {
            $bookings = Booking::whereHas('asset', fn ($q) => $q->where('category', $category))
                ->where('status', 'completed')
                ->where('created_at', '>=', $from)
                ->get();

            $avgDailyRate  = $bookings->avg('daily_rate') ?? 0;
            $totalBookings = $bookings->count();
            $utilisation   = $this->calculateUtilisation($category, $from);

            return [
                'category'          => $category,
                'period'            => $period,
                'avg_daily_rate_kes' => round($avgDailyRate, 2),
                'total_bookings'    => $totalBookings,
                'utilisation_pct'   => $utilisation,
                'demand_trend'      => $this->getDemandTrend($category, $from),
                'top_counties'      => $this->getTopCounties($category, $from),
                'generated_at'      => now()->toISOString(),
            ];
        });
    }

    /**
     * Return pricing index for a category+county combination.
     */
    public function getPricingIndex(string $category, string $county): array
    {
        $cacheKey = "pricing_index:{$category}:{$county}";
        return Cache::remember($cacheKey, 1800, function () use ($category, $county) {
            $recentBookings = Booking::whereHas('asset', fn ($q) =>
                $q->where('category', $category)
                  ->where('county', $county)
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

            $prevBookings = Booking::whereHas('asset', fn ($q) =>
                $q->where('category', $category)
                  ->where('county', $county)
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
            ->get();

            $currentAvg = $recentBookings->avg('daily_rate') ?? 0;
            $prevAvg    = $prevBookings->avg('daily_rate') ?? $currentAvg;
            $trend      = $prevAvg > 0 ? round((($currentAvg - $prevAvg) / $prevAvg) * 100, 1) : 0;

            return [
                'category'    => $category,
                'county'      => $county,
                'current_avg_daily_rate' => round($currentAvg, 2),
                'prev_avg_daily_rate'    => round($prevAvg, 2),
                'trend_pct'   => $trend,
                'direction'   => $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable'),
                'sample_size' => $recentBookings->count(),
                'updated_at'  => now()->toISOString(),
            ];
        });
    }

    /**
     * AI-assisted fleet valuation estimate based on comparable data.
     */
    public function getFleetValuationEstimate(array $assetSpecs): array
    {
        $category     = $assetSpecs['category'] ?? 'passenger_car';
        $year         = $assetSpecs['year'] ?? now()->year;
        $make         = $assetSpecs['make'] ?? null;
        $currentYear  = now()->year;
        $ageYears     = $currentYear - $year;

        // Get comparable assets with listing prices
        $comparables = Asset::where('category', $category)
            ->when($make, fn ($q) => $q->where('make', $make))
            ->whereNotNull('purchase_value')
            ->where('is_active', true)
            ->limit(20)
            ->get();

        $avgValue     = $comparables->avg('purchase_value') ?? 0;
        $depreciation = min(0.7, $ageYears * 0.08); // 8% per year, max 70%
        $estimate     = round($avgValue * (1 - $depreciation), 2);

        return [
            'estimated_value' => $estimate,
            'depreciation_pct' => round($depreciation * 100, 1),
            'comparables_count' => $comparables->count(),
            'avg_comparable_value' => round($avgValue, 2),
            'methodology' => 'comparable_depreciation',
            'confidence' => $comparables->count() >= 5 ? 'high' : ($comparables->count() >= 2 ? 'medium' : 'low'),
        ];
    }

    /**
     * Log API usage for billing/throttling.
     */
    public function trackApiUsage(DataProductSubscription $sub, string $endpoint): void
    {
        $key = "data_api:usage:{$sub->id}:" . now()->format('Y-m-d');
        $current = Cache::increment($key);
        if ($current === 1) {
            Cache::expire($key, 86400);
        }
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function calculateUtilisation(string $category, $from): float
    {
        $totalAssets  = Asset::where('category', $category)->where('is_active', true)->count();
        if ($totalAssets === 0) return 0;

        $bookedDays = Booking::whereHas('asset', fn ($q) => $q->where('category', $category))
            ->where('status', 'completed')
            ->where('created_at', '>=', $from)
            ->sum('rental_days');

        $daysPeriod   = now()->diffInDays($from);
        $totalCapacity = $totalAssets * $daysPeriod;

        return $totalCapacity > 0 ? round(($bookedDays / $totalCapacity) * 100, 1) : 0;
    }

    private function getDemandTrend(string $category, $from): string
    {
        $recent = Booking::whereHas('asset', fn ($q) => $q->where('category', $category))
            ->where('created_at', '>=', $from)->count();

        $days = now()->diffInDays($from);
        $prevFrom = now()->subDays($days * 2);
        $prev = Booking::whereHas('asset', fn ($q) => $q->where('category', $category))
            ->whereBetween('created_at', [$prevFrom, $from])->count();

        if ($prev === 0) return 'stable';
        $change = (($recent - $prev) / $prev) * 100;
        return $change > 5 ? 'rising' : ($change < -5 ? 'falling' : 'stable');
    }

    private function getTopCounties(string $category, $from): array
    {
        return Booking::whereHas('asset', fn ($q) => $q->where('category', $category))
            ->where('status', 'completed')
            ->where('created_at', '>=', $from)
            ->join('assets', 'bookings.asset_id', '=', 'assets.id')
            ->select('assets.county', DB::raw('count(*) as booking_count'))
            ->groupBy('assets.county')
            ->orderByDesc('booking_count')
            ->limit(5)
            ->pluck('booking_count', 'assets.county')
            ->toArray();
    }
}
