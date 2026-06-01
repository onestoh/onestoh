<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\AiPricingSuggestion;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AiPricingService
{
    private array $rateFields = [
        'hourly'  => 'hourly_rate',
        'daily'   => 'daily_rate',
        'weekly'  => 'weekly_rate',
        'monthly' => 'monthly_rate',
    ];

    public function generateSuggestion(Asset $asset, string $durationType): AiPricingSuggestion
    {
        // Return non-expired suggestion if one already exists
        $existing = AiPricingSuggestion::where('asset_id', $asset->id)
            ->where('duration_type', $durationType)
            ->where('expires_at', '>', now())
            ->whereNull('owner_action')
            ->latest()
            ->first();

        if ($existing) {
            return $existing;
        }

        $comparables = $this->getComparableListings($asset, $durationType);
        $rateField   = $this->rateFields[$durationType] ?? 'daily_rate';
        $currentRate = $asset->{$rateField};

        if ($comparables->isEmpty()) {
            // Fallback: no market data
            return AiPricingSuggestion::create([
                'asset_id'                  => $asset->id,
                'duration_type'             => $durationType,
                'current_rate'              => $currentRate,
                'suggested_rate'            => $currentRate ?? 0,
                'market_avg'                => $currentRate ?? 0,
                'market_min'                => $currentRate ?? 0,
                'market_max'                => $currentRate ?? 0,
                'comparable_listings_count' => 0,
                'recommendation'            => 'maintain',
                'confidence_score'          => 10,
                'reasoning'                 => ['Insufficient market data to make a recommendation.'],
                'expires_at'                => now()->addDays(7),
            ]);
        }

        $rates = $comparables->pluck($rateField)->filter()->values();
        $avg   = $rates->avg();
        $min   = $rates->min();
        $max   = $rates->max();
        $count = $rates->count();

        // Seasonal index adjustment
        $seasonalIndex = $this->calculateSeasonalIndex($asset->category ?? '', now()->month);
        $adjustedAvg   = $avg * $seasonalIndex;

        // Determine recommendation
        $reasoning = [];
        if ($currentRate === null) {
            $recommendation = 'maintain';
            $suggestedRate  = $adjustedAvg;
            $reasoning[]    = 'No current rate set; suggested market average.';
        } elseif ($currentRate < $adjustedAvg * 0.85) {
            $recommendation = 'increase';
            $suggestedRate  = round($adjustedAvg * 0.95, 2);
            $reasoning[]    = "Your rate is more than 15% below market average of KES {$avg}. Increasing could boost revenue.";
        } elseif ($currentRate > $adjustedAvg * 1.2) {
            $recommendation = 'decrease';
            $suggestedRate  = round($adjustedAvg * 1.05, 2);
            $reasoning[]    = "Your rate is more than 20% above market average of KES {$avg}. Lowering may improve occupancy.";
        } else {
            $recommendation = 'maintain';
            $suggestedRate  = $currentRate;
            $reasoning[]    = "Your rate is competitive within the market range (KES {$min} – KES {$max}).";
        }

        if ($seasonalIndex > 1.1) {
            $reasoning[] = 'Seasonal demand is currently high; consider a premium.';
        } elseif ($seasonalIndex < 0.9) {
            $reasoning[] = 'Seasonal demand is currently low; a discount may maintain bookings.';
        }

        $confidence = min(95, 40 + ($count * 5));

        return AiPricingSuggestion::create([
            'asset_id'                  => $asset->id,
            'duration_type'             => $durationType,
            'current_rate'              => $currentRate,
            'suggested_rate'            => $suggestedRate,
            'market_avg'                => round($avg, 2),
            'market_min'                => round($min, 2),
            'market_max'                => round($max, 2),
            'comparable_listings_count' => $count,
            'recommendation'            => $recommendation,
            'confidence_score'          => $confidence,
            'reasoning'                 => $reasoning,
            'expires_at'                => now()->addDays(7),
        ]);
    }

    public function getComparableListings(Asset $asset, string $durationType): Collection
    {
        $rateField = $this->rateFields[$durationType] ?? 'daily_rate';

        $query = Asset::where('is_published', true)
            ->where('id', '!=', $asset->id)
            ->whereNotNull($rateField)
            ->where($rateField, '>', 0);

        if ($asset->category) {
            $query->where('category', $asset->category);
        }

        // Try coordinate-based radius first (20 km)
        if ($asset->latitude && $asset->longitude) {
            $lat = (float) $asset->latitude;
            $lng = (float) $asset->longitude;
            $radius = 20; // km
            $earthRadius = 6371;

            $haversine = "(
                {$earthRadius} * acos(
                    cos(radians({$lat}))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians({$lng}))
                    + sin(radians({$lat})) * sin(radians(latitude))
                )
            )";

            $nearby = (clone $query)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereRaw("{$haversine} <= {$radius}")
                ->get();

            if ($nearby->count() >= 5) {
                return $nearby;
            }
        }

        // Fallback: same county
        if ($asset->county) {
            $query->where('county', $asset->county);
        }

        return $query->limit(50)->get();
    }

    public function applyRecommendation(AiPricingSuggestion $suggestion, User $owner, ?float $customRate = null): void
    {
        $asset     = $suggestion->asset;
        $rateField = $this->rateFields[$suggestion->duration_type] ?? 'daily_rate';
        $rate      = $customRate ?? $suggestion->suggested_rate;

        $asset->update([$rateField => $rate]);

        $suggestion->update([
            'owner_action'       => $customRate !== null ? 'customised' : 'accepted',
            'owner_applied_rate' => $rate,
        ]);
    }

    public function generateBulkSuggestions(User $owner): array
    {
        $suggestions = [];
        $assets = Asset::where('user_id', $owner->id)->where('is_published', true)->get();

        foreach ($assets as $asset) {
            foreach (array_keys($this->rateFields) as $durationType) {
                $rateField = $this->rateFields[$durationType];
                if ($asset->{$rateField} !== null || $durationType === 'daily') {
                    $suggestions[] = $this->generateSuggestion($asset, $durationType);
                }
            }
        }

        return $suggestions;
    }

    public function getPeakSeasonForecast(string $category, string $county): array
    {
        $peaks = [];
        $months = range(1, 12);

        foreach ($months as $month) {
            $index = $this->calculateSeasonalIndex($category, $month);
            if ($index >= 1.2) {
                $peaks[] = [
                    'month'              => $month,
                    'month_name'         => date('F', mktime(0, 0, 0, $month, 1)),
                    'demand_index'       => $index,
                    'suggested_uplift'   => round(($index - 1) * 100, 1) . '%',
                    'description'        => $this->getPeakDescription($category, $month),
                ];
            }
        }

        return [
            'category'       => $category,
            'county'         => $county,
            'peak_periods'   => $peaks,
            'generated_at'   => now()->toISOString(),
        ];
    }

    public function calculateSeasonalIndex(string $category, int $month): float
    {
        $indices = [
            'car' => [1 => 1.0, 2 => 1.0, 3 => 0.9, 4 => 0.9, 5 => 1.0, 6 => 1.1, 7 => 1.2, 8 => 1.2, 9 => 1.0, 10 => 1.0, 11 => 1.1, 12 => 1.4],
            'suv' => [1 => 1.0, 2 => 1.0, 3 => 0.9, 4 => 0.9, 5 => 1.0, 6 => 1.1, 7 => 1.2, 8 => 1.2, 9 => 1.0, 10 => 1.0, 11 => 1.1, 12 => 1.4],
            'truck' => [1 => 1.1, 2 => 1.1, 3 => 0.8, 4 => 0.8, 5 => 1.0, 6 => 1.0, 7 => 1.1, 8 => 1.1, 9 => 1.0, 10 => 1.0, 11 => 1.0, 12 => 1.2],
            'construction_equipment' => [1 => 0.7, 2 => 0.7, 3 => 0.7, 4 => 1.0, 5 => 1.1, 6 => 1.2, 7 => 1.2, 8 => 1.1, 9 => 1.1, 10 => 1.0, 11 => 0.9, 12 => 0.8],
            'event_equipment' => [1 => 1.0, 2 => 1.1, 3 => 1.2, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0, 8 => 1.1, 9 => 1.0, 10 => 1.0, 11 => 1.1, 12 => 1.3],
        ];

        $catLower = strtolower($category);
        foreach ($indices as $key => $monthMap) {
            if (str_contains($catLower, $key)) {
                return $monthMap[$month] ?? 1.0;
            }
        }

        return 1.0;
    }

    private function getPeakDescription(string $category, int $month): string
    {
        $descriptions = [
            12 => 'December holiday season — high travel demand.',
            7  => 'July school holidays — elevated leisure travel.',
            8  => 'August school holidays — elevated leisure travel.',
            3  => 'March — Easter travel season begins.',
        ];

        return $descriptions[$month] ?? 'Seasonal demand uplift expected.';
    }
}
