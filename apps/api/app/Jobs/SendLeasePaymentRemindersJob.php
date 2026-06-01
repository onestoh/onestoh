<?php
namespace App\Jobs;

use App\Services\FinancingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Log;

class SendLeasePaymentRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function handle(FinancingService $service): void
    {
        Log::info('SendLeasePaymentRemindersJob started');
        $service->sendPaymentReminders();
        Log::info('SendLeasePaymentRemindersJob completed');
    }
}
