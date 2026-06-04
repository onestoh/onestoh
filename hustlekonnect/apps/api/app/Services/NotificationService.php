<?php
namespace App\Services;

use App\Models\User;
use App\Models\PlatformNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send(User $user, string $title, string $body, string $type = 'general', array $data = []): void
    {
        PlatformNotification::create([
            'id'      => (string) Str::uuid(),
            'user_id' => $user->id,
            'title'   => $title,
            'body'    => $body,
            'type'    => $type,
            'data'    => $data,
        ]);

        $this->sendSms($user->phone, $body);
        $this->sendPush($user, $title, $body, $data);
    }

    public function sendOtp(User $user, string $otp): void
    {
        $message = "Your HustleKonnect verification code is: {$otp}. Valid for 10 minutes. Do not share.";
        $this->sendSms($user->phone, $message);
    }

    private function sendSms(string $phone, string $message): void
    {
        if (empty(config('services.africastalking.api_key'))) return;

        try {
            Http::withHeaders([
                'apiKey'       => config('services.africastalking.api_key'),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept'       => 'application/json',
            ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                'username' => config('services.africastalking.username'),
                'to'       => $phone,
                'message'  => $message,
                'from'     => config('services.africastalking.sender_id', 'HustleKnt'),
            ]);
        } catch (\Exception $e) {
            Log::warning("SMS send failed to {$phone}: " . $e->getMessage());
        }
    }

    private function sendPush(User $user, string $title, string $body, array $data): void
    {
        if (empty(config('services.firebase.server_key')) || empty($user->fcm_token)) return;

        try {
            Http::withHeaders([
                'Authorization' => 'key=' . config('services.firebase.server_key'),
                'Content-Type'  => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to'           => $user->fcm_token,
                'notification' => ['title' => $title, 'body' => $body],
                'data'         => $data,
            ]);
        } catch (\Exception $e) {
            Log::warning("Push notification failed for user {$user->id}: " . $e->getMessage());
        }
    }
}
