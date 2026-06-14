<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlatformSetting;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'company_name'                  => 'TheOnlineYard',
            'company_tagline'               => "Kenya's Premier Vehicle & Machinery Rental Platform",
            'company_email'                 => 'info@theonlineyard.co.ke',
            'company_phone'                 => '+254 700 000 000',
            'company_address'               => 'Nairobi, Kenya',
            'company_website'               => 'https://theonlineyard.co.ke',
            'company_logo'                  => '',
            'company_favicon'               => '',
            'company_mission'               => 'To make heavy machinery and vehicle rental accessible, affordable, and safe for all Kenyans.',
            'company_vision'                => 'To be the leading digital marketplace for asset rentals across Africa.',
            'terms_and_conditions'          => 'By using TheOnlineYard, you agree to our terms and conditions...',
            'privacy_policy'                => 'We value your privacy...',
            'letterhead_footer'             => 'TheOnlineYard Ltd | Nairobi, Kenya | info@theonlineyard.co.ke',
            'primary_color'                 => '#E8922A',
            'secondary_color'               => '#2ECC8A',
            'gradient_start'                => '#E8922A',
            'gradient_end'                  => '#E84040',
            'dark_mode'                     => 'true',
            'default_language'              => 'en',
            'default_country'               => 'KE',
            'default_currency'              => 'KES',
            'timezone'                      => 'Africa/Nairobi',
            'platform_fee_percentage'       => '10',
            'security_deposit_percentage'   => '20',
            'min_booking_hours'             => '4',
            'mfa_enabled'                   => 'true',
            'mfa_method'                    => 'email',
            'smtp_host'                     => '',
            'smtp_port'                     => '587',
            'smtp_username'                 => '',
            'smtp_from_name'                => 'TheOnlineYard',
            'smtp_from_email'               => 'noreply@theonlineyard.co.ke',
            'mpesa_env'                     => 'sandbox',
            'support_whatsapp'              => '+254700000000',
            'facebook_url'                  => '',
            'twitter_url'                   => '',
            'instagram_url'                 => '',
            // legacy keys
            'referral_commission'           => '3',
            'licensed_broker_commission'    => '5',
            'min_payout_amount'             => '500',
        ];

        foreach ($defaults as $key => $value) {
            PlatformSetting::firstOrCreate(['key' => $key], ['key' => $key, 'value' => $value]);
        }
    }
}
