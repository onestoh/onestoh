<?php
namespace App\Jobs;

use App\Models\ErpIntegration;
use App\Services\ErpIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Log;

class SyncErpDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(private ErpIntegration $integration) {}

    public function handle(ErpIntegrationService $service): void
    {
        try {
            if ($this->integration->isTokenExpired()) {
                $service->refreshToken($this->integration);
                $this->integration->refresh();
            }

            $invoices = $service->syncInvoices($this->integration);
            $expenses = $service->syncExpenses($this->integration);
            $contacts = $service->syncContacts($this->integration);

            Log::info('ERP sync completed', [
                'integration_id' => $this->integration->id,
                'platform'       => $this->integration->platform,
                'invoices'       => $invoices,
                'expenses'       => $expenses,
                'contacts'       => $contacts,
            ]);

            $this->integration->update(['last_error' => null]);
        } catch (\Throwable $e) {
            $service->handleSyncError($this->integration, new \Exception($e->getMessage()));
            $this->fail($e);
        }
    }
}
