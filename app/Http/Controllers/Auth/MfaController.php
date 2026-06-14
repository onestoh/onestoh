<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MfaController extends Controller
{
    public function showVerify()
    {
        if (!session()->has('mfa_user_id')) {
            return redirect()->route('login');
        }
        $user = User::find(session('mfa_user_id'));
        return view('auth.mfa-verify', compact('user'));
    }

    public function sendCode()
    {
        $userId = session('mfa_user_id');
        if (!$userId) return redirect()->route('login');

        $user = User::findOrFail($userId);
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update(['mfa_code' => $code, 'mfa_code_expires_at' => now()->addMinutes(10)]);

        Mail::raw(
            "Your TheOnlineYard login verification code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you did not request this, ignore this message.",
            fn($m) => $m->to($user->email)->subject('Your Login Verification Code — TheOnlineYard')
        );

        return back()->with('info', 'A new code has been sent to ' . $this->maskEmail($user->email));
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $userId = session('mfa_user_id');
        if (!$userId) return redirect()->route('login');

        $user = User::findOrFail($userId);

        if ($user->mfa_code !== $request->code || now()->isAfter($user->mfa_code_expires_at)) {
            return back()->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        $user->update(['mfa_code' => null, 'mfa_code_expires_at' => null]);
        session()->forget(['mfa_user_id', 'mfa_remember']);
        Auth::login($user, session('mfa_remember', false));

        return redirect()->intended('/dashboard');
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        return substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 3)) . '@' . $domain;
    }
}
