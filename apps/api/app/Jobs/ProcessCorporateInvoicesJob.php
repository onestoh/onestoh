<?php
namespace App\Jobs;

use App\Models\CorporateAccount;
use App\Services\CorporateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessCorporateInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CorporateService $corporateService): void
    {
        $previousMonth = now()->subMonthNoOverflow()->format('Y-m');

        CorporateAccount::where('is_active', true)->each(function (CorporateAccount $account) use ($corporateService, $previousMonth) {
            $invoice = $corporateService->getMonthlyInvoice($account, $previousMonth);

            if (!empty($invoice['line_items'])) {
                Mail::to($account->billing_email)->send(
                    new \App\Mail\CorporateInvoiceMail($account, $invoice)
                );
            }
        });
    }
}
