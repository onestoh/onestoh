<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\DemandForecast;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DemandForecastService
{
    private AiPricingService $pricingService;

    public function __construct(AiPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function getForecast(string $category, string $county, string $period = 'month'): DemandForecast
    {
        $forecastDate = now()->startOf($period === 'day' ? 'day' : ($period === 'week' ? 'week' : 'month'))->toDateString();

        $cached = DemandForecast::where('category', $category)
            ->where('county', $county)
            ->where('forecast_date', $forecastDate)
            ->where('period_type', $period)
            ->first();

        if ($cached) {
            return $cached;
        }

        return $this->computeForecast($category, $county, $forecastDate, $period);
    }

    public function generateForecasts(): void
    {
        // Get all distinct category+county combos from assets
        $combos = Asset::where('is_published', true)
            ->whereNotNull('category')
            ->whereNotNull('county')
            ->select('category', 'county')
            ->distinct()
            ->get();

        foreach ($combos as $combo) {
            foreach (['day', 'week', 'month'] as $period) {
                $forecastDate = now()->startOf($period === 'day' ? 'day' : ($period === 'week' ? 'week' : 'month'))->toDateString();
                try {
                    $this->computeForecast($combo->category, $combo->county, $forecastDate, $period);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Forecast generation failed', [
                        'category' => $combo->category, 'county' => $combo->county, 'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    private function computeForecast(string $category, string $county, string $forecastDate, string $period): DemandForecast
    {
        // Historical booking count for same period last year
        $lastYearStart = now()->subYear()->startOf($period === 'day' ? 'day' : ($period === 'week' ? 'week' : 'month'));
        $lastYearEnd   = now()->subYear()->endOf($period === 'day' ? 'day' : ($period === 'week' ? 'week' : 'month'));

        $historicalCount = Booking::whereHas('asset', function ($q) use ($category, $county) {
            $q->where('category', $category)->where('county', $county);
        })->whereBetween('created_at', [$lastYearStart, $lastYearEnd])->count();

        // Apply seasonal index
        $month         = now()->month;
        $seasonalIndex = $this->pricingService->calculateSeasonalIndex($category, $month);

        $predictedCount  = round($historicalCount * $seasonalIndex, 2);
        $demandIndex     = $seasonalIndex;
        $confidence      = $historicalCount > 20 ? 80 : ($historicalCount > 5 ? 60 : 35);

        $drivers = $this->getDemandDrivers($month);

        return DemandForecast::updateOrCreate(
            ['category' => $category, 'county' => $county, 'forecast_date' => $forecastDate, 'period_type' => $period],
            [
                'predicted_demand_index'  => $demandIndex,
                'predicted_booking_count' => $predictedCount,
                'confidence_score'        => $confidence,
                'demand_drivers'          => $drivers,
            ]
        );
    }

    public function getUpcomingDemandSpikes(User $owner): array
    {
        $alerts  = [];
        $assets  = Asset::where('user_id', $owner->id)->whereNotNull('category')->whereNotNull('county')->get();
        $checked = [];

        foreach ($assets as $asset) {
            $key = $asset->category . '|' . $asset->county;
            if (in_array($key, $checked)) continue;
            $checked[] = $key;

            for ($i = 0; $i < 90; $i++) {
                $date  = now()->addDays($i);
                $month = (int) $date->format('n');
                $index = $this->pricingService->calculateSeasonalIndex($asset->category, $month);

                if ($index >= 1.2 && $date->day === 1) {
                    $upliftPct = round(($index - 1) * 100);
                    $alerts[]  = [
                        'asset_category' => $asset->category,
                        'county'         => $asset->county,
                        'period'         => $date->format('F Y'),
                        'demand_index'   => $index,
                        'message'        => ucfirst($asset->category) . " demand in {$asset->county} expected to be {$upliftPct}% above average in " . $date->format('F') . '.',
                        'suggested_action' => 'Consider applying AI pricing recommendations to maximise revenue.',
                    ];
                }
            }
        }

        return $alerts;
    }

    public function getFleetInvestmentSignal(User $owner): array
    {
        $signals = [];
        $assets  = Asset::where('user_id', $owner->id)->get();

        $categoryBreakdown = $assets->groupBy('category');

        foreach ($categoryBreakdown as $category => $fleet) {
            $county     = $fleet->first()->county ?? 'Nairobi';
            $fleetCount = $fleet->count();

            // Predict next month's demand
            $nextMonth    = now()->addMonth()->month;
            $demandIndex  = $this->pricingService->calculateSeasonalIndex($category, $nextMonth);

            $forecast = DemandForecast::where('category', $category)
                ->where('county', $county)
                ->where('period_type', 'month')
                ->orderByDesc('forecast_date')
                ->first();

            $predictedBookings = $forecast ? $forecast->predicted_booking_count : ($fleetCount * 10 * $demandIndex);
            $capacityUtilisation = $fleetCount > 0 ? min(100, round(($predictedBookings / ($fleetCount * 30)) * 100, 1)) : 0;

            $signal = [
                'category'             => $category,
                'county'               => $county,
                'current_fleet_size'   => $fleetCount,
                'predicted_bookings'   => $predictedBookings,
                'capacity_utilisation' => $capacityUtilisation . '%',
                'demand_index'         => $demandIndex,
            ];

            if ($capacityUtilisation > 85) {
                $signal['recommendation'] = "High demand forecast for {$category} in {$county}. Consider expanding your fleet to capture additional revenue.";
                $signal['signal']         = 'expand';
            } elseif ($capacityUtilisation < 40) {
                $signal['recommendation'] = "Low demand forecast for {$category} in {$county}. Consider listing assets in higher-demand counties.";
                $signal['signal']         = 'redistribute';
            } else {
                $signal['recommendation'] = "Fleet size appears well-matched to predicted demand for {$category} in {$county}.";
                $signal['signal']         = 'hold';
            }

            $signals[] = $signal;
        }

        return $signals;
    }

    private function getDemandDrivers(int $month): array
    {
        $drivers = [
            12 => ['Christmas holiday', 'New Year holiday', 'School holiday'],
            1  => ['New Year', 'Post-holiday travel'],
            3  => ['Easter (some years)', 'End of long dry season'],
            4  => ['Easter (some years)', 'Long rains begin'],
            6  => ['Madaraka Day', 'School holiday'],
            7  => ['School holiday', 'July peak travel'],
            8  => ['School holiday'],
            10 => ['Huduma Namba period', 'Short rains start'],
            11 => ['Short rains peak'],
        ];

        return $drivers[$month] ?? [];
    }
}
