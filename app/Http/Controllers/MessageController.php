<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'body'         => 'required|string|max:2000',
            'subject'      => 'nullable|string|max:255',
            'property_id'  => 'nullable|integer|exists:properties,id',
        ]);

        $userId = session('user_id');

        $message = Message::create([
            'sender_id'    => $userId,
            'recipient_id' => $request->recipient_id,
            'body'         => $request->body,
            'subject'      => $request->subject,
            'property_id'  => $request->property_id,
            'is_read'      => false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message_id' => $message->id,
            ]);
        }

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    public function inbox()
    {
        $userId = session('user_id');

        $messages = Message::where('recipient_id', $userId)
            ->with('sender')
            ->latest()
            ->paginate(20);

        return response()->json($messages);
    }

    public function conversation($userId)
    {
        $currentUserId = session('user_id');

        $messages = Message::where(function ($q) use ($currentUserId, $userId) {
            $q->where('sender_id', $currentUserId)->where('recipient_id', $userId);
        })->orWhere(function ($q) use ($currentUserId, $userId) {
            $q->where('sender_id', $userId)->where('recipient_id', $currentUserId);
        })
        ->orderBy('created_at')
        ->get();

        // Mark all unread messages from the other user as read
        Message::where('sender_id', $userId)
            ->where('recipient_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }
}
