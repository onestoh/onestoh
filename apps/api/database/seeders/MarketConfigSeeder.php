<?php
namespace Database\Seeders;

use App\Services\MarketExpansionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketConfigSeeder extends Seeder
{
    public function run(MarketExpansionService $service): void
    {
        // Kenya (Phase 1 — primary market)
        DB::table('market_configs')->updateOrInsert(
            ['country_code' => 'KE'],
            [
                'country_name'      => 'Kenya',
                'currency_code'     => 'KES',
                'currency_symbol'   => 'KSh',
                'timezone'          => 'Africa/Nairobi',
                'phone_prefix'      => '+254',
                'phone_format'      => '^(\\+?254|0)[71]\\d{8}$',
                'payment_gateways'  => json_encode(['mpesa', 'stripe', 'pesapal']),
                'id_document_types' => json_encode(['national_id', 'passport', 'alien_card']),
                'language_code'     => 'en',
                'is_launched'       => true,
                'is_beta'           => false,
                'launch_date'       => '2023-01-01',
                'regulatory_notes'  => json_encode(['regulator' => 'CBK', 'vat_rate' => 16]),
                'updated_at'        => now(),
                'created_at'        => now(),
            ]
        );

        // Uganda (Phase 2)
        DB::table('market_configs')->updateOrInsert(
            ['country_code' => 'UG'],
            [
                'country_name'      => 'Uganda',
                'currency_code'     => 'UGX',
                'currency_symbol'   => 'USh',
                'timezone'          => 'Africa/Kampala',
                'phone_prefix'      => '+256',
                'phone_format'      => '^(\\+?256|0)[7][0-9]\\d{7}$',
                'payment_gateways'  => json_encode(['mtn_momo_ug', 'airtel_money']),
                'id_document_types' => json_encode(['national_id', 'passport']),
                'language_code'     => 'en',
                'is_launched'       => true,
                'is_beta'           => false,
                'launch_date'       => '2023-06-01',
                'regulatory_notes'  => json_encode(['regulator' => 'BOU', 'vat_rate' => 18]),
                'updated_at'        => now(),
                'created_at'        => now(),
            ]
        );

        // Tanzania (Phase 3)
        DB::table('market_configs')->updateOrInsert(
            ['country_code' => 'TZ'],
            [
                'country_name'      => 'Tanzania',
                'currency_code'     => 'TZS',
                'currency_symbol'   => 'TSh',
                'timezone'          => 'Africa/Dar_es_Salaam',
                'phone_prefix'      => '+255',
                'phone_format'      => '^(\\+?255|0)[67]\\d{8}$',
                'payment_gateways'  => json_encode(['mpesa_tz', 'tigopesa', 'airtel_tz']),
                'id_document_types' => json_encode(['nida', 'passport']),
                'language_code'     => 'sw',
                'is_launched'       => true,
                'is_beta'           => true,
                'launch_date'       => '2024-01-01',
                'regulatory_notes'  => json_encode(['regulator' => 'BOT', 'vat_rate' => 18]),
                'updated_at'        => now(),
                'created_at'        => now(),
            ]
        );

        // Nigeria, Ghana, South Africa (Phase 4)
        $service->seedNigeriaConfig();
        $service->seedGhanaConfig();
        $service->seedSouthAfricaConfig();

        $this->command->info('MarketConfigSeeder: 6 markets seeded (KE, UG, TZ, NG, GH, ZA).');
    }
}
