<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Asset, FinancingApplication, FinancingPartner, LeaseToOwnAgreement};
use App\Services\FinancingService;
use Illuminate\Http\{JsonResponse, Request};

class FinancingController extends Controller
{
    public function __construct(private FinancingService $service) {}

    /**
     * Return eligible financing partners for an asset, with repayment preview.
     */
    public function getEligiblePartners(Asset $asset): JsonResponse
    {
        $user     = auth()->user();
        $partners = $this->service->getEligiblePartners($asset, $user);

        $result = $partners->map(function (FinancingPartner $p) use ($asset) {
            $assetValue = $asset->purchase_value ?? 0;
            $deposit    = $assetValue * ($p->min_deposit_pct / 100);
            $principal  = $assetValue - $deposit;
            $monthly    = $p->monthlyPayment($principal, $p->min_interest_rate_pa, $p->min_tenure_months);
            return array_merge($p->toArray(), [
                'sample_monthly_payment' => $monthly,
                'sample_deposit'         => round($deposit, 2),
            ]);
        });

        return response()->json(['data' => $result]);
    }

    /**
     * Calculate a repayment schedule on the fly.
     */
    public function calculateRepayment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'financing_partner_id' => 'required|exists:financing_partners,id',
            'asset_value'          => 'required|numeric|min:1',
            'deposit_amount'       => 'required|numeric|min:0',
            'tenure_months'        => 'required|integer|min:3|max:84',
        ]);

        $partner  = FinancingPartner::findOrFail($data['financing_partner_id']);
        $schedule = $this->service->calculateRepaymentSchedule(
            $partner,
            $data['asset_value'],
            $data['deposit_amount'],
            $data['tenure_months']
        );

        return response()->json(['data' => $schedule, 'count' => count($schedule)]);
    }

    /**
     * Submit a financing application.
     */
    public function submitApplication(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_id'             => 'sometimes|exists:assets,id',
            'financing_partner_id' => 'required|exists:financing_partners,id',
            'type'                 => 'required|in:purchase_loan,lease_to_own,fleet_credit_line',
            'asset_value'          => 'required|numeric|min:1',
            'deposit_amount'       => 'required|numeric|min:0',
            'tenure_months'        => 'required|integer|min:3|max:84',
            'documents'            => 'sometimes|array',
        ]);

        $app = $this->service->submitApplication($data, auth()->user());
        return response()->json(['data' => $app], 201);
    }

    /**
     * List the authenticated user's financing applications.
     */
    public function getMyApplications(): JsonResponse
    {
        $apps = FinancingApplication::where('user_id', auth()->id())
            ->with(['partner', 'asset'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($apps);
    }

    /**
     * Get status and details of a specific application.
     */
    public function getApplicationStatus(FinancingApplication $app): JsonResponse
    {
        abort_if($app->user_id !== auth()->id(), 403);
        return response()->json(['data' => $app->load(['partner', 'asset', 'leaseAgreement'])]);
    }

    /**
     * List the user's active lease-to-own agreements.
     */
    public function getLeaseAgreements(): JsonResponse
    {
        $leases = LeaseToOwnAgreement::where('lessee_id', auth()->id())
            ->with(['asset', 'application.partner'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $leases]);
    }

    /**
     * Get the full repayment schedule for a lease agreement.
     */
    public function getLeaseSchedule(LeaseToOwnAgreement $agreement): JsonResponse
    {
        abort_if($agreement->lessee_id !== auth()->id(), 403);

        $schedule = [];
        $balance  = $agreement->total_lease_value;
        $rate     = ($agreement->application->interest_rate_pa / 100) / 12;
        $monthly  = $agreement->monthly_payment;

        for ($i = 1; $i <= $agreement->total_months; $i++) {
            $interest = round($balance * $rate, 2);
            $principal = round($monthly - $interest, 2);
            $balance   = max(0, round($balance - $principal, 2));

            $schedule[] = [
                'month'     => $i,
                'payment'   => $monthly,
                'principal' => $principal,
                'interest'  => $interest,
                'balance'   => $balance,
                'paid'      => $i <= $agreement->months_paid,
            ];
        }

        return response()->json([
            'data'            => $schedule,
            'summary'         => [
                'total_months'    => $agreement->total_months,
                'months_paid'     => $agreement->months_paid,
                'completion_pct'  => $agreement->completionPct(),
                'remaining_amount'=> $agreement->remainingAmount(),
                'next_payment_due'=> $agreement->next_payment_due,
                'status'          => $agreement->status,
            ],
        ]);
    }
}
