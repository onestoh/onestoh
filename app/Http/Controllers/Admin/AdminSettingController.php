<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function index()
    {
        $s = PlatformSetting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('s'));
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'company');

        if ($tab === 'company') {
            $request->validate([
                'company_name'      => 'required|string|max:100',
                'company_email'     => 'required|email',
                'company_phone'     => 'nullable|string|max:30',
                'company_address'   => 'nullable|string|max:255',
                'company_website'   => 'nullable|url|max:255',
                'company_tagline'   => 'nullable|string|max:255',
                'company_mission'   => 'nullable|string',
                'company_vision'    => 'nullable|string',
                'letterhead_footer' => 'nullable|string|max:500',
                'company_logo'      => 'nullable|image|max:2048',
                'company_favicon'   => 'nullable|image|max:512',
            ]);
            $keys = ['company_name','company_email','company_phone','company_address','company_website','company_tagline','company_mission','company_vision','letterhead_footer'];
            foreach ($keys as $k) {
                PlatformSetting::set($k, $request->input($k, ''));
            }
            if ($request->hasFile('company_logo')) {
                $path = $request->file('company_logo')->store('settings', 'public');
                PlatformSetting::set('company_logo', $path);
            }
            if ($request->hasFile('company_favicon')) {
                $path = $request->file('company_favicon')->store('settings', 'public');
                PlatformSetting::set('company_favicon', $path);
            }
        }

        elseif ($tab === 'legal') {
            $request->validate([
                'terms_and_conditions' => 'nullable|string',
                'privacy_policy'       => 'nullable|string',
            ]);
            PlatformSetting::set('terms_and_conditions', $request->input('terms_and_conditions', ''));
            PlatformSetting::set('privacy_policy', $request->input('privacy_policy', ''));
        }

        elseif ($tab === 'theme') {
            $request->validate([
                'primary_color'   => 'required|string|max:10',
                'secondary_color' => 'required|string|max:10',
                'gradient_start'  => 'required|string|max:10',
                'gradient_end'    => 'required|string|max:10',
                'dark_mode'       => 'nullable|string',
            ]);
            foreach (['primary_color','secondary_color','gradient_start','gradient_end'] as $k) {
                PlatformSetting::set($k, $request->input($k));
            }
            PlatformSetting::set('dark_mode', $request->has('dark_mode') ? 'true' : 'false');
        }

        elseif ($tab === 'locale') {
            $request->validate([
                'default_language' => 'required|string|max:10',
                'default_country'  => 'required|string|max:5',
                'default_currency' => 'required|string|max:5',
                'timezone'         => 'required|string|max:50',
            ]);
            foreach (['default_language','default_country','default_currency','timezone'] as $k) {
                PlatformSetting::set($k, $request->input($k));
            }
        }

        elseif ($tab === 'billing') {
            $request->validate([
                'platform_fee_percentage'     => 'required|numeric|min:0|max:50',
                'security_deposit_percentage' => 'required|numeric|min:0|max:100',
                'min_booking_hours'           => 'required|integer|min:1',
                'mpesa_env'                   => 'required|in:sandbox,production',
            ]);
            foreach (['platform_fee_percentage','security_deposit_percentage','min_booking_hours','mpesa_env'] as $k) {
                PlatformSetting::set($k, $request->input($k));
            }
        }

        elseif ($tab === 'notifications') {
            $keys = ['smtp_host','smtp_port','smtp_username','smtp_from_name','smtp_from_email','support_whatsapp'];
            foreach ($keys as $k) {
                PlatformSetting::set($k, $request->input($k, ''));
            }
        }

        elseif ($tab === 'social') {
            foreach (['facebook_url','twitter_url','instagram_url'] as $k) {
                PlatformSetting::set($k, $request->input($k, ''));
            }
        }

        elseif ($tab === 'security') {
            PlatformSetting::set('mfa_enabled', $request->has('mfa_enabled') ? 'true' : 'false');
            PlatformSetting::set('mfa_method', $request->input('mfa_method', 'email'));
        }

        return back()->with('success', 'Settings saved.');
    }
}
