<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Message;
use App\Models\NotificationLog;
use App\Models\Property;
use App\Models\RentPayment;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function properties(Request $request)
    {
        $user       = $request->user();
        $properties = Property::where('user_id', $user->id)->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $properties->items(),
            'meta'    => ['page' => $properties->currentPage(), 'total' => $properties->total()],
        ]);
    }

    public function leases(Request $request)
    {
        $user   = $request->user();
        $leases = Lease::with(['property:id,title,county,location', 'landlord:id,name', 'tenant:id,name'])
            ->where(function ($q) use ($user) {
                $q->where('tenant_id', $user->id)
                  ->orWhere('landlord_id', $user->id);
            })
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $leases->items(),
            'meta'    => ['page' => $leases->currentPage(), 'total' => $leases->total()],
        ]);
    }

    public function payments(Request $request)
    {
        $user     = $request->user();
        $payments = RentPayment::where('tenant_id', $user->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $payments->items(),
            'meta'    => ['page' => $payments->currentPage(), 'total' => $payments->total()],
        ]);
    }

    public function messages(Request $request)
    {
        $user     = $request->user();
        $messages = Message::with(['sender:id,name'])
            ->where('recipient_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $messages->items(),
            'meta'    => ['page' => $messages->currentPage(), 'total' => $messages->total()],
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'subject'      => 'required|string|max:255',
            'body'         => 'required|string',
        ]);

        $user    = $request->user();
        $message = Message::create([
            'sender_id'    => $user->id,
            'recipient_id' => $request->recipient_id,
            'subject'      => $request->subject,
            'body'         => $request->body,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $message,
        ], 201);
    }

    public function notifications(Request $request)
    {
        $user          = $request->user();
        $notifications = NotificationLog::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $notifications->items(),
            'meta'    => ['page' => $notifications->currentPage(), 'total' => $notifications->total()],
        ]);
    }
}
