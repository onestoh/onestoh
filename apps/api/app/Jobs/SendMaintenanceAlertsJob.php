<?php
namespace App\Jobs;

use App\Models\Asset;
use App\Models\MaintenanceSchedule;
use App\Services\FleetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMaintenanceAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(FleetService $fleetService): void
    {
        // Find all active schedules where next_due_at is within alert_days_before
        MaintenanceSchedule::where('is_active', true)
            ->whereNotNull('next_due_at')
            ->whereRaw('next_due_at <= DATE_ADD(CURDATE(), INTERVAL alert_days_before DAY)')
            ->with('asset.owner')
            ->each(function (MaintenanceSchedule $schedule) use ($fleetService) {
                $fleetService->scheduleMaintenanceAlert($schedule->asset);
            });
    }
}
