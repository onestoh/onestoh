<?php
namespace App\Console;

use App\Jobs\ExpireSlotHoldsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Release expired slot holds every 5 minutes
        $schedule->job(new ExpireSlotHoldsJob)->everyFiveMinutes();

        // Clean up old OTP records daily
        $schedule->command('model:prune', ['--model' => 'App\\Models\\OtpVerification'])
            ->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
