<?php
namespace App\Jobs;

use App\Models\FinancingApplication;
use App\Services\FinancingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Log;

class CheckFinancingApplicationStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function handle(FinancingService $service): void
    {
        $pending = FinancingApplication::whereIn('status', ['submitted', 'under_review'])
            ->whereNotNull('partner_reference')
            ->get();

        Log::info('CheckFinancingApplicationStatusJob: checking ' . $pending->count() . ' applications');

        foreach ($pending as $app) {
            try {
                $service->checkApplicationStatus($app);
            } catch (\Throwable $e) {
                Log::warning('Status check failed for application ' . $app->id, ['error' => $e->getMessage()]);
            }
        }
    }
}
