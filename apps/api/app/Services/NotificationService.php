<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\PlatformNotification;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    private Client $httpClient;

    // Notification templates keyed by type
    private array $templates = [
        'booking_confirmed' => [
            'title' => 'Booking Confirmed!',
            'body'  => 'Your booking #{booking_id} for {asset_name} has been confirmed. Pickup on {start_date}.',
        ],
        'booking_cancelled' => [
            'title' => 'Booking Cancelled',
            'body'  => 'Booking #{booking_id} has been cancelled. {reason}',
        ],
        'payment_received' => [
            'title' => 'Payment Received',
            'body'  => 'Payment of KES {amount} received for booking #{booking_id}.',
        ],
        'earnings_released' => [
            'title' => 'Earnings Released',
            'body'  => 'KES {amount} has been released to your wallet for booking #{booking_id}.',
        ],
        'dispute_opened' => [
            'title' => 'Dispute Opened',
            'body'  => 'A dispute has been raised for booking #{booking_id}. Our team will review within 48 hours.',
        ],
        'dispute_resolved' => [
            'title' => 'Dispute Resolved',
            'body'  => 'The dispute for booking #{booking_id} has been resolved. {ruling}',
        ],
        'kyc_approved' => [
            'title' => 'KYC Approved',
            'body'  => 'Your identity verification has been approved. You can now make bookings.',
        ],
        'kyc_rejected' => [
            'title' => 'KYC Rejected',
            'body'  => 'Your KYC submission was rejected. Reason: {reason}. Please resubmit.',
        ],
        'owner_notified' => [
            'title' => 'New Booking Request',
            'body'  => 'You have a new confirmed booking for {asset_name} starting {start_date}.',
        ],
        'active' => [
            'title' => 'Rental Started',
            'body'  => 'Your rental of {asset_name} is now active. Drive safely!',
        ],
        'completed' => [
            'title' => 'Rental Completed',
            'body'  => 'Rental #{booking_id} completed. Please leave a review.',
        ],
        'payout_processed' => [
            'title' => 'Payout Processed',
            'body'  => 'Your payout of KES {amount} has been sent to your M-Pesa {phone}.',
        ],
    ];

    public function __construct()
    {
        $this->httpClient = new Client(['timeout' => 15]);
    }

    /**
     * Send a notification to a user via all applicable channels.
     */
    public function send(User $user, string $type, array $data = [], array $channels = ['database', 'sms', 'email', 'push']): void
    {
        $template = $this->templates[$type] ?? null;
        if (!$template) {
            Log::warning('Unknown notification type', ['type' => $type]);
            return;
        }

        $title = $this->interpolate($template['title'], $data);
        $body  = $this->interpolate($template['body'], $data);

        $channelsSent = [];

        // Always save to database
        if (in_array('database', $channels)) {
            PlatformNotification::create([
                'user_id'       => $user->id,
                'type'          => $type,
                'title'         => $title,
                'body'          => $body,
                'data'          => $data,
                'channels_sent' => [],
            ]);
        }

        // SMS via Africa's Talking
        if (in_array('sms', $channels) && $user->phone) {
            try {
                $this->sendSms($user->phone, $body);
                $channelsSent['sms'] = true;
            } catch (\Throwable $e) {
                Log::error('SMS send failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                $channelsSent['sms'] = false;
            }
        }

        // Email via SendGrid (Laravel Mailer)
        if (in_array('email', $channels) && $user->email) {
            try {
                $this->sendEmail($user, $title, $body, $data);
                $channelsSent['email'] = true;
            } catch (\Throwable $e) {
                Log::error('Email send failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                $channelsSent['email'] = false;
            }
        }

        // Push via Firebase FCM
        if (in_array('push', $channels)) {
            $fcmToken = $user->fcm_token ?? null;
            if ($fcmToken) {
                try {
                    $this->sendPush($fcmToken, $title, $body, $data);
                    $channelsSent['push'] = true;
                } catch (\Throwable $e) {
                    Log::error('Push send failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                    $channelsSent['push'] = false;
                }
            }
        }

        // Update channels_sent on latest notification
        PlatformNotification::where('user_id', $user->id)
            ->where('type', $type)
            ->latest()
            ->first()
            ?->update(['channels_sent' => $channelsSent]);
    }

    /**
     * Send SMS via Africa's Talking API.
     */
    private function sendSms(string $phone, string $message): void
    {
        $apiKey   = config('services.africastalking.api_key');
        $username = config('services.africastalking.username');
        $sender   = config('services.africastalking.sender_id', 'TheYard');

        $response = $this->httpClient->post('https://api.africastalking.com/version1/messaging', [
            'headers' => [
                'apiKey'       => $apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept'       => 'application/json',
            ],
            'form_params' => [
                'username' => $username,
                'to'       => $phone,
                'message'  => $message,
                'from'     => $sender,
            ],
        ]);

        $result = json_decode($response->getBody()->getContents(), true);
        Log::debug('SMS sent', ['phone' => $phone, 'result' => $result]);
    }

    /**
     * Send email via Laravel mailer (configured for SendGrid SMTP).
     */
    private function sendEmail(User $user, string $subject, string $body, array $data): void
    {
        Mail::send('emails.notification', compact('user', 'subject', 'body', 'data'), function ($message) use ($user, $subject) {
            $message->to($user->email, $user->name)->subject($subject);
        });
    }

    /**
     * Send push notification via Firebase FCM v1 API.
     */
    private function sendPush(string $fcmToken, string $title, string $body, array $data): void
    {
        $projectId   = config('services.firebase.project_id');
        $accessToken = $this->getFirebaseAccessToken();

        $payload = [
            'message' => [
                'token'        => $fcmToken,
                'notification' => ['title' => $title, 'body' => $body],
                'data'         => array_map('strval', $data),
                'android'      => ['priority' => 'high'],
                'apns'         => ['headers' => ['apns-priority' => '10']],
            ],
        ];

        $this->httpClient->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type'  => 'application/json',
            ],
            'json' => $payload,
        ]);
    }

    /**
     * Get Firebase access token using service account key (cached 50 min).
     */
    private function getFirebaseAccessToken(): string
    {
        return \Illuminate\Support\Facades\Cache::remember('firebase_access_token', 3000, function () {
            $serviceAccountPath = config('services.firebase.service_account_path');
            if (!$serviceAccountPath || !file_exists($serviceAccountPath)) {
                throw new \RuntimeException('Firebase service account not configured.');
            }
            $sa = json_decode(file_get_contents($serviceAccountPath), true);
            // Build JWT for service account
            $now    = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claim  = base64_encode(json_encode([
                'iss'   => $sa['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));
            $toSign = "{$header}.{$claim}";
            openssl_sign($toSign, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);
            $jwt = "{$toSign}." . base64_encode($signature);

            $response = $this->httpClient->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'];
        });
    }

    private function interpolate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $template = str_replace("{{$key}}", $value, $template);
            }
        }
        return $template;
    }

    /**
     * Notify both parties of a booking event.
     */
    public function notifyBookingParties(Booking $booking, string $type, array $extraData = []): void
    {
        $data = array_merge([
            'booking_id' => $booking->id,
            'asset_name' => $booking->asset->make . ' ' . $booking->asset->model,
            'start_date' => $booking->start_at->format('d M Y H:i'),
            'end_date'   => $booking->end_at->format('d M Y H:i'),
        ], $extraData);

        $this->send($booking->client, $type, $data);
        $this->send($booking->asset->owner, $type, $data);

        if ($booking->broker_id) {
            $this->send($booking->broker, $type, $data);
        }
    }
}
