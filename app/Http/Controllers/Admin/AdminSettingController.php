<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = PlatformSetting::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'platform_fee_percentage'      => 'required|numeric|min:0|max:50',
            'security_deposit_percentage'  => 'required|numeric|min:0|max:100',
            'min_booking_hours'            => 'required|integer|min:1',
        ]);

        foreach ($request->only(['platform_fee_percentage', 'security_deposit_percentage', 'min_booking_hours']) as $key => $value) {
            PlatformSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Settings updated.');
    }
}
