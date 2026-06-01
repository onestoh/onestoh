<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiChatSession;
use App\Services\AiChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AiChatController extends Controller
{
    public function __construct(private AiChatService $service) {}

    public function startSession(Request $request): JsonResponse
    {
        $session = $this->service->startSession($request->user());
        return response()->json([
            'session_id'    => $session->id,
            'session_token' => $session->session_token,
        ]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'session_token' => 'required|string',
            'message'       => 'required|string|max:2000',
        ]);

        $session = AiChatSession::where('session_token', $request->session_token)->firstOrFail();

        if (!$this->service->moderateMessage($request->message)) {
            return response()->json(['error' => 'Message contains disallowed content'], 422);
        }

        $response = $this->service->chat($session, $request->message);

        return response()->json([
            'response'      => $response,
            'session_id'    => $session->id,
            'message_count' => $session->fresh()->message_count,
        ]);
    }

    public function getHistory(Request $request, string $sessionToken): JsonResponse
    {
        $session = AiChatSession::where('session_token', $sessionToken)->firstOrFail();
        return response()->json(['messages' => $session->messages ?? []]);
    }

    public function escalate(Request $request, string $sessionToken): JsonResponse
    {
        $session = AiChatSession::where('session_token', $sessionToken)->firstOrFail();
        $this->service->escalateToHuman($session);
        return response()->json(['message' => 'Escalated to human support. Our team will contact you shortly.']);
    }
}
