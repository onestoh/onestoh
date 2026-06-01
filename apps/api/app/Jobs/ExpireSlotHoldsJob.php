<?php
namespace App\Jobs;

use App\Services\AvailabilityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpireSlotHoldsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(AvailabilityService $availabilityService): void
    {
        $released = $availabilityService->releaseExpiredHolds();

        Log::info('ExpireSlotHoldsJob: released expired holds', ['count' => $released]);
    }
}
