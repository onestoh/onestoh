<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlatformSetting;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'platform_fee_percentage', 'value' => '10', 'description' => 'Platform fee percentage on each booking'],
            ['key' => 'referral_commission', 'value' => '3', 'description' => 'Referral commission percentage'],
            ['key' => 'licensed_broker_commission', 'value' => '5', 'description' => 'Licensed broker commission percentage'],
            ['key' => 'min_payout_amount', 'value' => '500', 'description' => 'Minimum payout amount in KES'],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
