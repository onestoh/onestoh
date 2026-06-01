<?php
namespace App\Console;

use App\Jobs\ExpirePromotionsJob;
use App\Jobs\ProcessCorporateInvoicesJob;
use App\Jobs\RefreshAssetAnalyticsJob;
use App\Jobs\SendMaintenanceAlertsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Expire slot holds every 5 minutes (Phase 1 job)
        if (class_exists(\App\Jobs\ExpireSlotHoldsJob::class)) {
            $schedule->job(new \App\Jobs\ExpireSlotHoldsJob)->everyFiveMinutes();
        }

        // Send maintenance alerts daily at 08:00
        $schedule->job(new SendMaintenanceAlertsJob)->dailyAt('08:00');

        // Expire listing promotions every hour
        $schedule->job(new ExpirePromotionsJob)->hourly();

        // Generate corporate invoices on 1st of each month at 06:00
        $schedule->job(new ProcessCorporateInvoicesJob)->monthlyOn(1, '06:00');

        // Refresh all asset analytics caches daily at 02:00
        $schedule->job(new RefreshAssetAnalyticsJob)->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
