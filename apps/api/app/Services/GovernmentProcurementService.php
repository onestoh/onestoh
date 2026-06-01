<?php
namespace App\Services;

use App\Models\{Asset, Booking, ProcurementTender, TenderBid, User, Yard};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\{DB, Log, Notification};
use App\Notifications\TenderAwardedNotification;
use App\Notifications\TenderOpportunityNotification;
use App\Notifications\TenderRejectedNotification;

class GovernmentProcurementService
{
    /**
     * Return open tenders with optional filters.
     */
    public function getOpenTenders(array $filters = []): Collection
    {
        return ProcurementTender::with('entity')
            ->where('status', 'open')
            ->where('submission_deadline', '>=', now()->toDateString())
            ->when(!empty($filters['category']), fn ($q) => $q->where('asset_category', $filters['category']))
            ->when(!empty($filters['county']), fn ($q) => $q->whereHas('entity', fn ($eq) => $eq->where('county', $filters['county'])))
            ->when(!empty($filters['max_budget']), fn ($q) => $q->where('budget_per_unit', '<=', $filters['max_budget']))
            ->orderBy('submission_deadline')
            ->get();
    }

    /**
     * Validate and submit a yard's bid for a tender.
     */
    public function submitBid(ProcurementTender $tender, Yard $yard, array $data): TenderBid
    {
        abort_if(!$tender->isOpen(), 422, 'Tender is no longer accepting bids.');
        abort_if(TenderBid::where('tender_id', $tender->id)->where('yard_id', $yard->id)->exists(), 422, 'Bid already submitted for this tender.');

        // Verify the yard has enough verified assets
        $offeredAssetIds = collect($data['offered_assets'])->pluck('asset_id');
        $verifiedCount   = Asset::whereIn('id', $offeredAssetIds)
            ->where('yard_id', $yard->id)
            ->where('is_verified', true)
            ->count();

        abort_if($verifiedCount < $tender->quantity_required, 422, 'Insufficient verified assets for this tender.');

        return TenderBid::create([
            'tender_id'            => $tender->id,
            'yard_id'              => $yard->id,
            'owner_id'             => $yard->owner_id,
            'offered_assets'       => $data['offered_assets'],
            'total_bid_value'      => $data['rate_per_unit'] * $tender->quantity_required * $tender->duration_value,
            'rate_per_unit'        => $data['rate_per_unit'],
            'proposal_notes'       => $data['proposal_notes'] ?? null,
            'compliance_documents' => $data['compliance_documents'] ?? [],
            'status'               => 'submitted',
        ]);
    }

    /**
     * Score bids: price 40%, yard rating 30%, availability 20%, verification 10%.
     */
    public function evaluateBids(ProcurementTender $tender): array
    {
        $bids    = $tender->bids()->with('yard')->get();
        $minRate = $bids->min('rate_per_unit');
        $scored  = [];

        foreach ($bids as $bid) {
            $priceScore    = $minRate > 0 ? ($minRate / $bid->rate_per_unit) * 40 : 40;
            $ratingScore   = (($bid->yard->average_rating ?? 3) / 5) * 30;

            $allConfirmed  = collect($bid->offered_assets)->every(fn ($a) => $a['availability_confirmed'] ?? false);
            $availScore    = $allConfirmed ? 20 : 10;

            $verifiedAssets = Asset::whereIn('id', collect($bid->offered_assets)->pluck('asset_id'));
            $verPct         = $verifiedAssets->count() > 0
                ? ($verifiedAssets->where('is_verified', true)->count() / $verifiedAssets->count()) * 10
                : 0;

            $total = round($priceScore + $ratingScore + $availScore + $verPct, 2);

            $scored[] = [
                'bid_id'        => $bid->id,
                'yard_id'       => $bid->yard_id,
                'yard_name'     => $bid->yard->name,
                'rate_per_unit' => $bid->rate_per_unit,
                'price_score'   => round($priceScore, 2),
                'rating_score'  => round($ratingScore, 2),
                'avail_score'   => $availScore,
                'verify_score'  => round($verPct, 2),
                'total_score'   => $total,
            ];
        }

        usort($scored, fn ($a, $b) => $b['total_score'] <=> $a['total_score']);
        return $scored;
    }

    /**
     * Award a tender: create bulk bookings, notify all bidders.
     */
    public function awardTender(ProcurementTender $tender, TenderBid $winningBid): void
    {
        DB::transaction(function () use ($tender, $winningBid) {
            $winningBid->update(['status' => 'awarded', 'awarded_at' => now()]);
            $tender->update(['status' => 'awarded']);

            // Reject other bids
            TenderBid::where('tender_id', $tender->id)
                ->where('id', '!=', $winningBid->id)
                ->update(['status' => 'rejected', 'rejection_reason' => 'Another bid was selected.']);

            // Create bookings for each offered asset
            $assets = collect($winningBid->offered_assets)->take($tender->quantity_required);
            foreach ($assets as $offered) {
                Booking::create([
                    'asset_id'          => $offered['asset_id'],
                    'renter_id'         => null, // government entity, linked via tender
                    'owner_id'          => $winningBid->owner_id,
                    'tender_bid_id'     => $winningBid->id,
                    'start_date'        => $tender->service_start_date,
                    'end_date'          => $tender->service_end_date,
                    'total_amount'      => $winningBid->rate_per_unit * $tender->duration_value,
                    'status'            => 'confirmed',
                    'payment_status'    => 'pending',
                    'source'            => 'government_tender',
                ]);
            }
        });

        // Notifications
        $winningBid->owner->notify(new TenderAwardedNotification($tender, $winningBid));

        TenderBid::where('tender_id', $tender->id)
            ->where('id', '!=', $winningBid->id)
            ->with('owner')
            ->each(fn ($bid) => $bid->owner->notify(new TenderRejectedNotification($tender)));
    }

    /**
     * Generate compliance summary for a yard.
     */
    public function generateComplianceReport(Yard $yard): array
    {
        $assets         = Asset::where('yard_id', $yard->id)->get();
        $verifiedCount  = $assets->where('is_verified', true)->count();
        $insuredCount   = $assets->where('has_insurance', true)->count();
        $ntsaCount      = $assets->where('has_ntsa_cert', true)->count();

        return [
            'yard_id'               => $yard->id,
            'yard_name'             => $yard->name,
            'total_assets'          => $assets->count(),
            'verified_assets'       => $verifiedCount,
            'insured_assets'        => $insuredCount,
            'ntsa_certified_assets' => $ntsaCount,
            'compliance_score'      => $assets->count() > 0
                ? round((($verifiedCount + $insuredCount + $ntsaCount) / ($assets->count() * 3)) * 100, 1)
                : 0,
            'eligible_for_tenders'  => $verifiedCount >= 1,
        ];
    }

    /**
     * Notify yards that have matching verified assets for a new tender.
     */
    public function notifyEligibleYards(ProcurementTender $tender): void
    {
        $eligibleOwners = User::whereHas('yards.assets', function ($q) use ($tender) {
            $q->where('category', $tender->asset_category)
              ->where('is_verified', true);
        })->get();

        foreach ($eligibleOwners as $owner) {
            $owner->notify(new TenderOpportunityNotification($tender));
        }
    }
}
