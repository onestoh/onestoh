<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'         => 'Kenya Vehicle Pricing Index',
                'slug'         => 'ke-vehicle-pricing-index',
                'description'  => 'Real-time daily hire rate indices for all vehicle categories across 47 counties in Kenya. Updated hourly from live marketplace data.',
                'type'         => 'pricing_index',
                'audience'     => 'insurance',
                'price_monthly'=> 9900.00,
                'price_annual' => 89000.00,
                'api_endpoint' => '/api/v1/data/pricing-index',
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'East Africa Demand Forecast API',
                'slug'         => 'ea-demand-forecast-api',
                'description'  => 'Monthly demand forecasts for equipment and vehicle hire across KE, UG, TZ. Includes seasonality patterns, event-driven demand spikes, and utilisation projections.',
                'type'         => 'demand_forecast',
                'audience'     => 'financing',
                'price_monthly'=> 24900.00,
                'price_annual' => 229000.00,
                'api_endpoint' => '/api/v1/data/market-report',
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Fleet Valuation API',
                'slug'         => 'fleet-valuation-api',
                'description'  => 'On-demand fleet asset valuation estimates based on comparable market data. Supports bulk valuation for insurance underwriting and loan collateral assessments.',
                'type'         => 'fleet_valuation_api',
                'audience'     => 'financing',
                'price_monthly'=> 49900.00,
                'price_annual' => 449000.00,
                'api_endpoint' => '/api/v1/data/fleet-valuation',
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Market Intelligence Monthly Report',
                'slug'         => 'market-intelligence-monthly',
                'description'  => 'Comprehensive monthly PDF + API report covering market trends, utilisation rates, operator performance benchmarks, and pan-African expansion opportunities.',
                'type'         => 'market_report',
                'audience'     => 'research',
                'price_monthly'=> 14900.00,
                'price_annual' => 139000.00,
                'api_endpoint' => '/api/v1/data/market-report',
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        foreach ($products as $product) {
            DB::table('data_products')->updateOrInsert(
                ['slug' => $product['slug']],
                $product
            );
        }

        $this->command->info('DataProductSeeder: 4 products seeded.');
    }
}
