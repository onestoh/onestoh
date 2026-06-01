<?php
namespace App\Jobs;

use App\Services\MultiCurrencyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchCurrencyRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private MultiCurrencyService $service) {}

    public function handle(): void
    {
        $this->service->fetchRates();
    }
}
