<?php
namespace App\Jobs;

use App\Models\Asset;
use App\Services\AnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshAssetAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private ?Asset $asset = null) {}

    public function handle(AnalyticsService $analyticsService): void
    {
        if ($this->asset) {
            app(\App\Services\FleetService::class)->refreshAnalyticsCache($this->asset);
            return;
        }

        // No specific asset — refresh all
        $analyticsService->refreshAllAssetCaches();
    }
}
