<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancingPartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            [
                'name'                     => 'KCB Bank Kenya',
                'type'                     => 'bank',
                'logo_url'                 => 'https://cdn.theonlineyard.co.ke/logos/kcb.png',
                'country_code'             => 'KE',
                'min_loan_amount'          => 500000.00,
                'max_loan_amount'          => 50000000.00,
                'min_interest_rate_pa'     => 13.00,
                'max_interest_rate_pa'     => 17.50,
                'min_tenure_months'        => 12,
                'max_tenure_months'        => 60,
                'min_deposit_pct'          => 20,
                'api_endpoint'             => null,
                'api_key_encrypted'        => null,
                'is_active'                => true,
                'eligible_asset_categories'=> json_encode(null),
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
            [
                'name'                     => 'Equity Bank Kenya',
                'type'                     => 'bank',
                'logo_url'                 => 'https://cdn.theonlineyard.co.ke/logos/equity.png',
                'country_code'             => 'KE',
                'min_loan_amount'          => 300000.00,
                'max_loan_amount'          => 30000000.00,
                'min_interest_rate_pa'     => 14.00,
                'max_interest_rate_pa'     => 18.00,
                'min_tenure_months'        => 6,
                'max_tenure_months'        => 48,
                'min_deposit_pct'          => 25,
                'api_endpoint'             => null,
                'api_key_encrypted'        => null,
                'is_active'                => true,
                'eligible_asset_categories'=> json_encode(['passenger_car', 'suv_4x4', 'pickup_truck', 'van_minibus']),
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
            [
                'name'                     => 'NCBA Bank Kenya',
                'type'                     => 'bank',
                'logo_url'                 => 'https://cdn.theonlineyard.co.ke/logos/ncba.png',
                'country_code'             => 'KE',
                'min_loan_amount'          => 1000000.00,
                'max_loan_amount'          => 100000000.00,
                'min_interest_rate_pa'     => 12.50,
                'max_interest_rate_pa'     => 16.00,
                'min_tenure_months'        => 12,
                'max_tenure_months'        => 72,
                'min_deposit_pct'          => 15,
                'api_endpoint'             => null,
                'api_key_encrypted'        => null,
                'is_active'                => true,
                'eligible_asset_categories'=> json_encode(null),
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
            [
                'name'                     => 'Stanbic Bank Uganda',
                'type'                     => 'bank',
                'logo_url'                 => 'https://cdn.theonlineyard.co.ke/logos/stanbic.png',
                'country_code'             => 'UG',
                'min_loan_amount'          => 10000000.00, // UGX
                'max_loan_amount'          => 500000000.00,
                'min_interest_rate_pa'     => 20.00,
                'max_interest_rate_pa'     => 25.00,
                'min_tenure_months'        => 12,
                'max_tenure_months'        => 48,
                'min_deposit_pct'          => 30,
                'api_endpoint'             => null,
                'api_key_encrypted'        => null,
                'is_active'                => true,
                'eligible_asset_categories'=> json_encode(null),
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
        ];

        foreach ($partners as $partner) {
            DB::table('financing_partners')->updateOrInsert(
                ['name' => $partner['name'], 'country_code' => $partner['country_code']],
                $partner
            );
        }

        $this->command->info('FinancingPartnerSeeder: 4 partners seeded.');
    }
}
