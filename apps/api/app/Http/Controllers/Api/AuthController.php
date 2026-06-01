<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\User;
use App\Models\Wallet;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|unique:users,phone|regex:/^\+?[0-9]{9,15}$/',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'     => 'sometimes|in:yard_owner,individual_owner,client,broker,driver',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $request->role ?? 'client',
        ]);

        // Generate referral code
        $user->update(['referral_code' => $user->generateReferralCode()]);

        // Create wallet
        Wallet::create(['user_id' => $user->id]);

        // Send phone OTP
        $this->sendOtp($user, 'phone');

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Please verify your phone number.',
            'user'    => $user->fresh(),
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string', // email or phone
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user  = User::where($field, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if ($user->trashed()) {
            return response()->json(['message' => 'Account suspended.'], 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function sendPhoneOtp(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->sendOtp($user, 'phone');
        return response()->json(['message' => 'OTP sent to ' . $user->phone]);
    }

    public function verifyPhoneOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['otp' => 'required|string|size:6']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $result = $this->verifyOtp($user, 'phone', $request->otp);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        $user->update(['phone_verified_at' => now()]);

        return response()->json(['message' => 'Phone verified successfully.']);
    }

    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['otp' => 'required|string|size:6']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user   = $request->user();
        $result = $this->verifyOtp($user, 'email', $request->otp);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        $user->update(['email_verified_at' => now()]);

        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('wallet', 'driver'));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'sometimes|string|max:100',
            'county'       => 'sometimes|string|max:60',
            'address'      => 'sometimes|string|max:255',
            'company_name' => 'sometimes|string|max:100',
            'kra_pin'      => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $request->user()->update($request->only(['name', 'county', 'address', 'company_name', 'kra_pin']));

        return response()->json(['user' => $request->user()->fresh()]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password'     => ['required', Password::min(8)->mixedCase()->numbers()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        $user->tokens()->delete();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['message' => 'Password changed.', 'token' => $token]);
    }

    private function sendOtp(User $user, string $type): void
    {
        $target = $type === 'phone' ? $user->phone : $user->email;
        $otp    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'user_id'    => $user->id,
            'type'       => $type,
            'otp'        => Hash::make($otp),
            'target'     => $target,
            'expires_at' => now()->addMinutes(10),
        ]);

        if ($type === 'phone') {
            $this->notifications->send($user, 'phone_otp', ['otp' => $otp, 'expires_in' => '10 minutes'], ['sms']);
        } else {
            $this->notifications->send($user, 'email_otp', ['otp' => $otp, 'expires_in' => '10 minutes'], ['email']);
        }
    }

    private function verifyOtp(User $user, string $type, string $otp): array
    {
        $record = OtpVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$record) {
            return ['success' => false, 'message' => 'OTP expired or not found. Please request a new one.'];
        }

        if ($record->attempts >= 5) {
            return ['success' => false, 'message' => 'Too many failed attempts. Please request a new OTP.'];
        }

        if (!Hash::check($otp, $record->otp)) {
            $record->increment('attempts');
            return ['success' => false, 'message' => 'Invalid OTP.'];
        }

        $record->update(['verified_at' => now()]);
        return ['success' => true];
    }
}
