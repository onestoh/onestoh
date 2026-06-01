<?php
namespace App\Services;

use App\Models\{Asset, FinancingApplication, FinancingPartner, LeaseToOwnAgreement, User};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\{Http, Log, Mail, Notification};
use App\Notifications\FinancingApplicationSubmittedNotification;
use App\Notifications\LeasePaymentReminderNotification;
use App\Jobs\CheckFinancingApplicationStatusJob;

class FinancingService
{
    /**
     * Return financing partners eligible for the asset and user's country.
     */
    public function getEligiblePartners(Asset $asset, User $user): Collection
    {
        $countryCode = $user->country_code ?? 'KE';
        $category    = $asset->category ?? null;

        return FinancingPartner::where('is_active', true)
            ->where('country_code', $countryCode)
            ->where(function ($q) use ($category) {
                $q->whereNull('eligible_asset_categories')
                  ->orWhereJsonContains('eligible_asset_categories', $category);
            })
            ->orderBy('min_interest_rate_pa')
            ->get();
    }

    /**
     * Build a full amortisation schedule.
     * Returns array of ['month', 'payment', 'principal', 'interest', 'balance'].
     */
    public function calculateRepaymentSchedule(
        FinancingPartner $partner,
        float $assetValue,
        float $deposit,
        int $months
    ): array {
        $principal = $assetValue - $deposit;
        $rate      = ($partner->min_interest_rate_pa / 100) / 12;
        $monthly   = $partner->monthlyPayment($principal, $partner->min_interest_rate_pa, $months);

        $schedule = [];
        $balance  = $principal;
        for ($i = 1; $i <= $months; $i++) {
            $interest  = round($balance * $rate, 2);
            $prinPay   = round($monthly - $interest, 2);
            $balance   = round($balance - $prinPay, 2);

            $schedule[] = [
                'month'     => $i,
                'payment'   => $monthly,
                'principal' => $prinPay,
                'interest'  => $interest,
                'balance'   => max(0, $balance),
            ];
        }
        return $schedule;
    }

    /**
     * Create and submit a financing application.
     */
    public function submitApplication(array $data, User $user): FinancingApplication
    {
        $partner = FinancingPartner::findOrFail($data['financing_partner_id']);
        $loanAmount = $data['asset_value'] - $data['deposit_amount'];
        $monthly    = $partner->monthlyPayment($loanAmount, $partner->min_interest_rate_pa, $data['tenure_months']);

        $app = FinancingApplication::create([
            'user_id'               => $user->id,
            'asset_id'              => $data['asset_id'] ?? null,
            'financing_partner_id'  => $partner->id,
            'type'                  => $data['type'],
            'asset_value'           => $data['asset_value'],
            'requested_amount'      => $loanAmount,
            'deposit_amount'        => $data['deposit_amount'],
            'tenure_months'         => $data['tenure_months'],
            'interest_rate_pa'      => $partner->min_interest_rate_pa,
            'monthly_repayment'     => $monthly,
            'submitted_documents'   => $data['documents'] ?? [],
            'status'                => 'submitted',
            'submitted_at'          => now(),
        ]);

        // Attempt to push to partner API if configured
        if ($partner->api_endpoint) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . decrypt($partner->api_key_encrypted),
                ])->post($partner->api_endpoint . '/applications', [
                    'reference'    => 'TOY-' . $app->id,
                    'amount'       => $loanAmount,
                    'tenure'       => $data['tenure_months'],
                    'asset_value'  => $data['asset_value'],
                    'customer_id'  => $user->id,
                ]);

                if ($response->successful()) {
                    $app->update(['partner_reference' => $response->json('reference')]);
                }
            } catch (\Throwable $e) {
                Log::warning('Financing partner API push failed', [
                    'app_id' => $app->id,
                    'error'  => $e->getMessage(),
                ]);
            }
        }

        $user->notify(new FinancingApplicationSubmittedNotification($app));

        return $app->fresh();
    }

    /**
     * Poll partner API for latest application status.
     */
    public function checkApplicationStatus(FinancingApplication $app): void
    {
        $partner = $app->partner;
        if (!$partner->api_endpoint || !$app->partner_reference) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . decrypt($partner->api_key_encrypted),
            ])->get($partner->api_endpoint . '/applications/' . $app->partner_reference);

            if ($response->successful()) {
                $remoteStatus = $response->json('status');
                $statusMap    = [
                    'pending'   => 'under_review',
                    'approved'  => 'approved',
                    'rejected'  => 'rejected',
                    'disbursed' => 'disbursed',
                ];

                if (isset($statusMap[$remoteStatus]) && $app->status !== $statusMap[$remoteStatus]) {
                    $updates = ['status' => $statusMap[$remoteStatus], 'decision_at' => now()];
                    if ($remoteStatus === 'rejected') {
                        $updates['rejection_reason'] = $response->json('reason');
                    }
                    if ($remoteStatus === 'disbursed') {
                        $updates['disbursed_at'] = now();
                    }
                    $app->update($updates);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Application status check failed', [
                'app_id' => $app->id,
                'error'  => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create lease-to-own agreement once application is disbursed.
     */
    public function createLeaseAgreement(FinancingApplication $app): LeaseToOwnAgreement
    {
        $startDate = now()->toDateString();
        $endDate   = now()->addMonths($app->tenure_months)->toDateString();

        return LeaseToOwnAgreement::create([
            'financing_application_id' => $app->id,
            'asset_id'                 => $app->asset_id,
            'lessee_id'                => $app->user_id,
            'total_lease_value'        => $app->requested_amount,
            'monthly_payment'          => $app->monthly_repayment,
            'total_months'             => $app->tenure_months,
            'months_paid'              => 0,
            'balloon_payment'          => 0,
            'residual_value'           => 0,
            'start_date'               => $startDate,
            'end_date'                 => $endDate,
            'next_payment_due'         => now()->addMonth()->toDateString(),
            'status'                   => 'active',
        ]);
    }

    /**
     * Record a monthly lease repayment.
     */
    public function processMonthlyRepayment(LeaseToOwnAgreement $agreement, string $paymentRef): void
    {
        $agreement->increment('months_paid');
        $agreement->update([
            'next_payment_due' => Carbon::parse($agreement->next_payment_due)->addMonth()->toDateString(),
        ]);

        if ($agreement->months_paid >= $agreement->total_months) {
            $hasBalloon = $agreement->balloon_payment > 0;
            if (!$hasBalloon) {
                $agreement->update([
                    'status'                    => 'completed',
                    'ownership_transferred'     => true,
                    'ownership_transferred_at'  => now(),
                ]);

                // Update asset ownership
                $agreement->asset()->update(['owner_id' => $agreement->lessee_id]);

                $agreement->lessee->notify(new \App\Notifications\OwnershipTransferredNotification($agreement));
            } else {
                // Balloon payment still pending — notify lessee
                $agreement->lessee->notify(new \App\Notifications\BalloonPaymentDueNotification($agreement));
            }
        }
    }

    /**
     * Send payment reminders to lessees with dues in 3 days.
     */
    public function sendPaymentReminders(): void
    {
        $targetDate = now()->addDays(3)->toDateString();

        LeaseToOwnAgreement::where('status', 'active')
            ->whereDate('next_payment_due', $targetDate)
            ->with('lessee')
            ->each(function (LeaseToOwnAgreement $agreement) {
                $agreement->lessee->notify(new LeasePaymentReminderNotification($agreement));
            });
    }
}
