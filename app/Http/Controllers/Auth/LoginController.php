<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (PlatformSetting::get('mfa_enabled', 'true') !== 'true') {
            return redirect()->intended($this->redirectTo);
        }

        Auth::logout();
        $request->session()->put('mfa_user_id', $user->id);
        $request->session()->put('mfa_remember', $request->boolean('remember'));

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update(['mfa_code' => $code, 'mfa_code_expires_at' => now()->addMinutes(10)]);

        Mail::raw(
            "Your TheOnlineYard login verification code is: {$code}\n\nThis code expires in 10 minutes.",
            fn($m) => $m->to($user->email)->subject('Your Login Verification Code — TheOnlineYard')
        );

        return redirect()->route('mfa.verify');
    }
}
