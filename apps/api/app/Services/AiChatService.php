<?php
namespace App\Services;

use App\Models\AiChatSession;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiChatService
{
    private string $apiKey;
    private string $model;
    private int $maxTokens;

    public function __construct()
    {
        $this->apiKey   = config('services.anthropic.api_key', '');
        $this->model    = config('services.anthropic.model', 'claude-haiku-4-5-20251001');
        $this->maxTokens = (int) config('services.anthropic.max_tokens', 1024);
    }

    public function startSession(?User $user): AiChatSession
    {
        return AiChatSession::create([
            'user_id'          => $user?->id,
            'session_token'    => Str::random(64),
            'messages'         => [],
            'message_count'    => 0,
            'tokens_used'      => 0,
            'escalated_to_human' => false,
            'last_activity_at' => now(),
        ]);
    }

    public function chat(AiChatSession $session, string $userMessage): string
    {
        if ($session->escalated_to_human) {
            return 'This chat has been escalated to our support team. A human agent will contact you shortly.';
        }

        // Append user message
        $session->addMessage('user', $userMessage);

        $messages = collect($session->fresh()->messages ?? [])
            ->map(fn($m) => ['role' => $m['role'], 'content' => $m['content']])
            ->toArray();

        $systemPrompt = $this->buildSystemPrompt($session->user);

        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => $this->model,
            'max_tokens' => $this->maxTokens,
            'system'     => $systemPrompt,
            'messages'   => $messages,
        ]);

        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error('Anthropic API error', ['status' => $response->status(), 'body' => $response->body()]);
            return 'I\'m having trouble processing your request right now. Please try again or contact support.';
        }

        $data          = $response->json();
        $assistantText = $data['content'][0]['text'] ?? 'Sorry, I could not generate a response.';
        $tokensUsed    = ($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0);

        // Persist assistant message
        $session->addMessage('assistant', $assistantText);
        $session->increment('tokens_used', $tokensUsed);

        return $assistantText;
    }

    public function buildSystemPrompt(?User $user): string
    {
        $base = "You are the YardOS AI assistant for TheOnlineYard, Kenya's premier asset rental marketplace. "
            . "Be helpful, concise, and professional. Always respond in the same language the user writes in (English or Swahili). "
            . "Do not discuss competitors. Do not provide legal or financial advice.\n\n";

        if (!$user) {
            return $base
                . "You are helping a potential customer explore the platform. "
                . "Help them understand how to search for assets, how booking works, payment options (M-Pesa, card), "
                . "and how to contact support. If they want to make a booking, direct them to sign up first.";
        }

        $roles = $user->roles ?? [];

        if (in_array('admin', $roles) || in_array('super_admin', $roles)) {
            return $base
                . "You are assisting a platform administrator. You have access to platform statistics and pending actions. "
                . "Help with fraud review workflows, tenant management, KYC queues, payout processing, and platform configuration. "
                . "Be precise and data-focused.";
        }

        if (in_array('owner', $roles)) {
            return $base
                . "You are assisting an asset owner/lender. Help them with: listing tips, photo requirements, pricing strategy, "
                . "understanding AI pricing suggestions, managing bookings, handling disputes, payout timelines, and growing their fleet. "
                . "Encourage them to use AI pricing tools and demand forecasts for better revenue.";
        }

        // Default: renter/client
        return $base
            . "You are assisting a renter/client. Help them with: how to search and filter assets, booking flow, payment methods (M-Pesa, card, mobile money), "
            . "cancellation and refund policy, how to submit damage reports, KYC verification process, and getting the best deals. "
            . "Be warm and friendly.";
    }

    public function escalateToHuman(AiChatSession $session): void
    {
        $session->update(['escalated_to_human' => true]);
        $session->addMessage('system', 'Chat escalated to human support agent.');

        // Notify support team
        \Illuminate\Support\Facades\Log::info('Chat escalated to human', [
            'session_id' => $session->id,
            'user_id'    => $session->user_id,
            'messages'   => $session->message_count,
        ]);

        // In production: fire notification to support channel (Slack/email)
        // \Notification::route('slack', config('services.slack.support_webhook'))->notify(new ChatEscalatedNotification($session));
    }

    public function moderateMessage(string $content): bool
    {
        $disallowed = [
            'bomb', 'kill', 'murder', 'terrorist', 'hack the', 'sql injection',
            'credit card number', 'cvv', 'pin number',
        ];

        $lower = strtolower($content);
        foreach ($disallowed as $term) {
            if (str_contains($lower, $term)) {
                return false;
            }
        }

        return true;
    }
}
