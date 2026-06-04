<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password'      => $request->password,
            'role'          => $request->role,
            'country'       => $request->country ?? 'KE',
            'preferred_currency' => $this->currencyForCountry($request->country ?? 'KE'),
            'referral_code' => strtoupper(Str::random(8)),
            'referred_by'   => $request->referral_code ? User::where('referral_code', $request->referral_code)->value('id') : null,
        ]);

        // Create wallet
        Wallet::create(['user_id' => $user->id, 'balance' => 0, 'currency' => $user->preferred_currency]);

        // Send OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("otp:{$user->id}", Hash::make($otp), now()->addMinutes(10));

        // In production, send via Africa's Talking SMS
        // app(NotificationService::class)->sendOtp($user, $otp);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Please verify your phone number.',
            'user'    => $this->userResource($user),
            'token'   => $token,
            'otp_sent' => true,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Account is suspended. Contact support.'], 403);
        }

        $user->update(['last_login_at' => now()]);
        $user->tokens()->where('name', '!=', 'api')->delete(); // revoke old tokens

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user'    => $this->userResource($user),
            'token'   => $token,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate(['otp' => 'required|string|size:6']);
        $user = $request->user();
        $cached = Cache::get("otp:{$user->id}");

        if (!$cached || !Hash::check($request->otp, $cached)) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        Cache::forget("otp:{$user->id}");
        $user->update(['email_verified_at' => now()]);

        return response()->json(['message' => 'Phone number verified successfully.']);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $user = $request->user();
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("otp:{$user->id}", Hash::make($otp), now()->addMinutes(10));
        // app(NotificationService::class)->sendOtp($user, $otp);
        return response()->json(['message' => 'OTP resent.']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $token = Str::random(64);
        Cache::put("pwd_reset:{$token}", $request->email, now()->addHour());
        // Send reset link via email
        return response()->json(['message' => 'Password reset link sent to your email.']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required', 'password' => 'required|min:8|confirmed']);
        $email = Cache::get("pwd_reset:{$request->token}");
        if (!$email) return response()->json(['message' => 'Invalid or expired reset token.'], 422);
        $user = User::where('email', $email)->firstOrFail();
        $user->update(['password' => $request->password]);
        Cache::forget("pwd_reset:{$request->token}");
        $user->tokens()->delete();
        return response()->json(['message' => 'Password reset successfully. Please log in.']);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->userResource($request->user()->load('wallet'))]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name'   => 'sometimes|string|min:2|max:100',
            'phone'  => 'sometimes|string|max:20',
            'country' => 'sometimes|string|size:2',
        ]);
        $request->user()->update($request->only('name', 'phone', 'country'));
        return response()->json(['message' => 'Profile updated.', 'data' => $this->userResource($request->user()->fresh())]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }
        $request->user()->update(['password' => $request->password]);
        $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();
        return response()->json(['message' => 'Password changed successfully.']);
    }

    private function userResource(User $user): array
    {
        return [
            'id'                 => $user->id,
            'name'               => $user->name,
            'email'              => $user->email,
            'phone'              => $user->phone,
            'role'               => $user->role,
            'kyc_status'         => $user->kyc_status,
            'country'            => $user->country,
            'preferred_currency' => $user->preferred_currency,
            'trust_score'        => $user->trust_score,
            'referral_code'      => $user->referral_code,
            'wallet_balance'     => $user->wallet?->balance,
            'is_active'          => $user->is_active,
            'email_verified_at'  => $user->email_verified_at,
            'created_at'         => $user->created_at,
        ];
    }

    private function currencyForCountry(string $country): string
    {
        return match($country) {
            'KE' => 'KES', 'UG' => 'UGX', 'TZ' => 'TZS',
            'NG' => 'NGN', 'GH' => 'GHS', 'ZA' => 'ZAR',
            default => 'KES',
        };
    }
}
