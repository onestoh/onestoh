<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        $ip        = $request->ip();
        $cacheKey  = "login_attempts_{$ip}";
        $attempts  = Cache::get($cacheKey, 0);

        if ($attempts >= 10) {
            $this->logSecurityEvent('LOGIN_BLOCKED', ['email' => $request->email]);
            return back()->withErrors(['email' => 'Too many login attempts. Try again in 15 minutes.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Cache::put($cacheKey, $attempts + 1, now()->addMinutes(15));
            $this->logSecurityEvent('LOGIN_FAILED', ['email' => $request->email, 'attempts' => $attempts + 1]);
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        if (!$user->is_active) {
            $this->logSecurityEvent('LOGIN_INACTIVE', ['email' => $request->email]);
            return back()->withErrors(['email' => 'Your account has been deactivated.'])->withInput();
        }

        Cache::forget($cacheKey);
        $this->logSecurityEvent('LOGIN_SUCCESS', ['email' => $request->email, 'user_id' => $user->id]);

        session([
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $user->role,
        ]);

        return redirect($this->dashboardRoute($user->role));
    }

    public function registerForm(Request $request)
    {
        return view('auth.register', ['role' => $request->role]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,landlord,broker_licensed,broker_unlicensed,tenant,developer,valuer,surveyor,auctioneer,investor,corporate,property_manager,finance',
        ]);

        $referralCode = $this->generateUniqueReferralCode();
        $referredBy   = null;

        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'phone'         => $request->phone,
            'referral_code' => $referralCode,
            'referred_by'   => $referredBy,
            'is_active'     => true,
            'is_verified'   => false,
        ]);

        session([
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $user->role,
        ]);

        // Send welcome email
        try {
            Mail::to($user->email)->queue(new WelcomeEmail($user));
        } catch (\Throwable $e) {
            Log::warning('WelcomeEmail failed: ' . $e->getMessage());
        }

        return redirect($this->dashboardRoute($user->role));
    }

    public function logout()
    {
        session()->flush();
        return redirect('/');
    }

    private function dashboardRoute(string $role): string
    {
        $map = [
            'admin'              => '/dashboard/admin',
            'landlord'           => '/dashboard/landlord',
            'broker_licensed'    => '/dashboard/broker',
            'broker_unlicensed'  => '/dashboard/promoter',
            'tenant'             => '/dashboard/tenant',
            'developer'          => '/dashboard/developer',
            'valuer'             => '/dashboard/valuer',
            'surveyor'           => '/dashboard/surveyor',
            'auctioneer'         => '/dashboard/auctioneer',
            'investor'           => '/dashboard/investor',
            'corporate'          => '/dashboard/corporate',
            'property_manager'   => '/dashboard/property-manager',
            'finance'            => '/dashboard/finance',
        ];

        return $map[$role] ?? '/dashboard/tenant';
    }

    private function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
