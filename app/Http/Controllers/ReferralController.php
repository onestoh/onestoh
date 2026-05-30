<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    /**
     * Generate or return existing referral code + link for the current user.
     */
    public function generate(Request $request)
    {
        $userId = session('user_id');
        $user   = User::findOrFail($userId);

        // Ensure user has a referral code
        if (empty($user->referral_code)) {
            do {
                $code = strtoupper(Str::random(6));
            } while (User::where('referral_code', $code)->exists());

            $user->update(['referral_code' => $code]);
        }

        // Get or create a Referral record for this user
        $referral = Referral::firstOrCreate(
            ['referrer_id' => $userId],
            [
                'referral_code'    => $user->referral_code,
                'click_count'      => 0,
                'conversion_count' => 0,
                'total_earned'     => 0,
            ]
        );

        return response()->json([
            'code' => $user->referral_code,
            'link' => url('/register?ref=' . $user->referral_code),
        ]);
    }

    /**
     * Track a referral click — set cookie, redirect to register.
     * GET /ref/{code}
     */
    public function track(Request $request, $code)
    {
        $referrer = User::where('referral_code', $code)->first();

        if ($referrer) {
            // Increment click count on the referral record
            Referral::where('referrer_id', $referrer->id)
                ->increment('click_count');
        }

        return redirect(url('/register?ref=' . $code))
            ->withCookie(cookie('ref_code', $code, 60 * 24 * 90)); // 90 days
    }
}
