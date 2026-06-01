<?php
namespace App\Console;

use App\Jobs\{
    CheckFinancingApplicationStatusJob,
    GenerateMarketReportsJob,
    SendLeasePaymentRemindersJob
};
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ── Phase 4 scheduled jobs ───────────────────────────────────────────
        $schedule->job(new SendLeasePaymentRemindersJob)->dailyAt('09:00');
        $schedule->job(new CheckFinancingApplicationStatusJob)->hourly();
        $schedule->job(new GenerateMarketReportsJob)->weekly();
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
