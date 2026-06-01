<?php
namespace App\Jobs;

use App\Services\DataMarketplaceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{Cache, Log};

class GenerateMarketReportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 600;

    private const CATEGORIES = [
        'passenger_car', 'suv_4x4', 'van_minibus', 'pickup_truck',
        'heavy_truck', 'excavator', 'tractor_farm', 'crane_lift', 'generator',
    ];

    private const COUNTIES = [
        'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret',
        'Thika', 'Nyeri', 'Machakos', 'Kilifi', 'Kakamega',
    ];

    public function handle(DataMarketplaceService $service): void
    {
        Log::info('GenerateMarketReportsJob started');

        foreach (self::CATEGORIES as $category) {
            foreach (['7d', '30d', 'quarter'] as $period) {
                try {
                    // Force regenerate by clearing old cache
                    Cache::forget("market_report:{$category}:{$period}");
                    $service->generateMarketReport($category, $period);
                } catch (\Throwable $e) {
                    Log::warning("Market report generation failed for {$category}/{$period}", ['error' => $e->getMessage()]);
                }
            }

            foreach (self::COUNTIES as $county) {
                try {
                    Cache::forget("pricing_index:{$category}:{$county}");
                    $service->getPricingIndex($category, $county);
                } catch (\Throwable $e) {
                    Log::warning("Pricing index generation failed for {$category}/{$county}", ['error' => $e->getMessage()]);
                }
            }
        }

        Log::info('GenerateMarketReportsJob completed');
    }
}
