<?php
namespace App\Services;

use App\Models\MarketConfig;
use Illuminate\Database\Eloquent\Collection;

class MarketExpansionService
{
    /**
     * Return all currently launched markets.
     */
    public function getLaunchedMarkets(): Collection
    {
        return MarketConfig::where('is_launched', true)->orderBy('country_name')->get();
    }

    /**
     * Return config for a specific country.
     */
    public function getMarketConfig(string $countryCode): ?MarketConfig
    {
        return MarketConfig::where('country_code', strtoupper($countryCode))->first();
    }

    /**
     * Validate a phone number format for a given market.
     */
    public function validatePhoneForMarket(string $phone, string $countryCode): bool
    {
        $config = $this->getMarketConfig($countryCode);
        if (!$config || !$config->phone_format) {
            return true; // permissive if no rule set
        }
        return (bool) preg_match('/' . $config->phone_format . '/', $phone);
    }

    /**
     * Return available payment gateways for a market.
     */
    public function getPaymentGatewaysForMarket(string $countryCode): array
    {
        $config = $this->getMarketConfig($countryCode);
        return $config ? ($config->payment_gateways ?? []) : [];
    }

    /**
     * Seed Nigeria market configuration.
     */
    public function seedNigeriaConfig(): void
    {
        MarketConfig::updateOrCreate(
            ['country_code' => 'NG'],
            [
                'country_name'     => 'Nigeria',
                'currency_code'    => 'NGN',
                'currency_symbol'  => '₦',
                'timezone'         => 'Africa/Lagos',
                'phone_prefix'     => '+234',
                'phone_format'     => '^(\\+?234|0)[789][01]\\d{8}$',
                'payment_gateways' => ['paystack', 'flutterwave'],
                'id_document_types' => ['nin', 'bvn', 'drivers_license', 'international_passport'],
                'language_code'    => 'en',
                'is_launched'      => true,
                'is_beta'          => false,
                'launch_date'      => '2024-04-01',
                'regulatory_notes' => [
                    'regulator'    => 'CBN',
                    'requires_cac' => true,
                    'vat_rate'     => 7.5,
                ],
            ]
        );
    }

    /**
     * Seed Ghana market configuration.
     */
    public function seedGhanaConfig(): void
    {
        MarketConfig::updateOrCreate(
            ['country_code' => 'GH'],
            [
                'country_name'     => 'Ghana',
                'currency_code'    => 'GHS',
                'currency_symbol'  => 'GH₵',
                'timezone'         => 'Africa/Accra',
                'phone_prefix'     => '+233',
                'phone_format'     => '^(\\+?233|0)[235]\\d{8}$',
                'payment_gateways' => ['paystack', 'mtn_momo_gh'],
                'id_document_types' => ['ghana_card', 'voters_id', 'passport', 'drivers_license'],
                'language_code'    => 'en',
                'is_launched'      => true,
                'is_beta'          => true,
                'launch_date'      => '2024-04-01',
                'regulatory_notes' => [
                    'regulator' => 'BOG',
                    'vat_rate'  => 15,
                ],
            ]
        );
    }

    /**
     * Seed South Africa market configuration.
     */
    public function seedSouthAfricaConfig(): void
    {
        MarketConfig::updateOrCreate(
            ['country_code' => 'ZA'],
            [
                'country_name'     => 'South Africa',
                'currency_code'    => 'ZAR',
                'currency_symbol'  => 'R',
                'timezone'         => 'Africa/Johannesburg',
                'phone_prefix'     => '+27',
                'phone_format'     => '^(\\+?27|0)[6-8]\\d{8}$',
                'payment_gateways' => ['payfast', 'peach_payments'],
                'id_document_types' => ['sa_id', 'passport', 'drivers_license'],
                'language_code'    => 'en',
                'is_launched'      => true,
                'is_beta'          => true,
                'launch_date'      => '2024-04-01',
                'regulatory_notes' => [
                    'regulator' => 'SARB',
                    'vat_rate'  => 15,
                    'cipc_required' => true,
                ],
            ]
        );
    }
}
