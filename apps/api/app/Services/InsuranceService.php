<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class InsuranceService
{
    private const CDW_RATE_DAILY_PCT = 0.015; // 1.5% of daily rate per day
    private const TPL_RATE_PER_DAY   = 150;   // KES 150/day

    /**
     * Return available insurance products with computed premiums.
     */
    public function getAvailableProducts(Asset $asset, string $durationType): array
    {
        // Determine number of days from duration type
        $days = match ($durationType) {
            'daily'   => 1,
            'weekly'  => 7,
            'monthly' => 30,
            default   => 1,
        };

        return [
            [
                'type'           => 'collision_damage_waiver',
                'name'           => 'Collision Damage Waiver',
                'description'    => 'Covers vehicle damage during rental period.',
                'premium'        => $this->calculatePremium($asset, 'collision_damage_waiver', $days),
                'coverage_limit' => (float) $asset->daily_rate * 500,
                'excess'         => 10000,
            ],
            [
                'type'           => 'third_party_liability',
                'name'           => 'Third Party Liability',
                'description'    => 'Covers damage or injury to third parties.',
                'premium'        => $this->calculatePremium($asset, 'third_party_liability', $days),
                'coverage_limit' => 5000000,
                'excess'         => 0,
            ],
            [
                'type'           => 'comprehensive',
                'name'           => 'Comprehensive Cover',
                'description'    => 'Full CDW + TPL coverage.',
                'premium'        => $this->calculatePremium($asset, 'comprehensive', $days),
                'coverage_limit' => (float) $asset->daily_rate * 500 + 5000000,
                'excess'         => 5000,
            ],
        ];
    }

    /**
     * Issue a policy for a booking.
     * Phase 2: certificate is a stub PDF. Phase 3 will call insurer API.
     */
    public function issuePolicy(Booking $booking, string $productType): InsurancePolicy
    {
        $asset = $booking->asset;
        $days  = max(1, (int) now()->parse($booking->starts_at)->diffInDays(now()->parse($booking->ends_at)));

        $premium  = $this->calculatePremium($asset, $productType, $days);
        $certPath = $this->generateCertificate($booking, $productType);

        return InsurancePolicy::create([
            'booking_id'      => $booking->id,
            'product_type'    => $productType,
            'insurer'         => 'TheOnlineYard Insurance (Stub)',
            'premium'         => $premium,
            'coverage_limit'  => $productType === 'third_party_liability' ? 5000000 : (float) $asset->daily_rate * 500,
            'excess_amount'   => $productType === 'third_party_liability' ? 0 : 10000,
            'valid_from'      => $booking->starts_at,
            'valid_to'        => $booking->ends_at,
            'status'          => 'active',
            'certificate_path'=> $certPath,
        ]);
    }

    /**
     * File an insurance claim.
     */
    public function fileClaim(InsurancePolicy $policy, User $claimant, array $data): InsuranceClaim
    {
        $claim = InsuranceClaim::create(array_merge($data, [
            'insurance_policy_id' => $policy->id,
            'filed_by'            => $claimant->id,
            'status'              => 'submitted',
        ]));

        $policy->update(['status' => 'claimed']);

        $claimant->notify(new \App\Notifications\InsuranceClaimSubmittedNotification($claim));

        return $claim;
    }

    /**
     * Calculate insurance premium.
     * CDW = 1.5% of daily rate × days
     * TPL = KES 150/day
     * Comprehensive = CDW + TPL
     */
    public function calculatePremium(Asset $asset, string $productType, int $days): float
    {
        $cdw = round((float) $asset->daily_rate * self::CDW_RATE_DAILY_PCT * $days, 2);
        $tpl = round(self::TPL_RATE_PER_DAY * $days, 2);

        return match ($productType) {
            'collision_damage_waiver' => $cdw,
            'third_party_liability'   => $tpl,
            'comprehensive'           => round($cdw + $tpl, 2),
            default                   => $cdw,
        };
    }

    private function generateCertificate(Booking $booking, string $productType): string
    {
        $path = 'insurance-certs/' . $booking->id . '-' . $productType . '.pdf';
        Storage::put($path, 'CERTIFICATE_PLACEHOLDER');
        return $path;
    }
}
