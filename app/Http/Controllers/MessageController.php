<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Get latest message per conversation (grouped by the other party)
        $messages = Message::where('from_user_id', $userId)
            ->orWhere('to_user_id', $userId)
            ->latest()
            ->get();

        $conversations = $messages
            ->groupBy(fn($m) => $m->from_user_id === $userId ? $m->to_user_id : $m->from_user_id)
            ->map(fn($msgs) => $msgs->first());

        $otherIds = $conversations->keys();
        $users = User::whereIn('id', $otherIds)->get()->keyBy('id');

        return view('messages.index', compact('conversations', 'users'));
    }

    public function thread(Booking $booking)
    {
        $myId = auth()->id();

        // Ensure user is part of this booking
        abort_unless(
            $booking->client_id === $myId || $booking->listing?->user_id === $myId,
            403
        );

        $messages = Message::where('booking_id', $booking->id)
            ->orderBy('created_at')
            ->get();

        // Mark received messages as read
        Message::where('booking_id', $booking->id)
            ->where('to_user_id', $myId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Determine the other party
        $otherUser = $booking->client_id === $myId
            ? $booking->listing?->user
            : $booking->client;

        return view('messages.thread', compact('messages', 'booking', 'otherUser'));
    }

    public function send(Request $request, Booking $booking)
    {
        $myId = auth()->id();

        abort_unless(
            $booking->client_id === $myId || $booking->listing?->user_id === $myId,
            403
        );

        $request->validate(['message' => 'required|string|max:2000']);

        $toUserId = $booking->client_id === $myId
            ? $booking->listing?->user_id
            : $booking->client_id;

        Message::create([
            'booking_id'   => $booking->id,
            'from_user_id' => $myId,
            'to_user_id'   => $toUserId,
            'message'      => $request->message,
        ]);

        return back();
    }
}
