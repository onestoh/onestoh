<?php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Property;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnquiryController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:20',
            'message'     => 'required|string|max:2000',
        ]);

        $property = Property::findOrFail($request->property_id);

        // Create message if user is logged in
        $senderId = session('user_id');
        if ($senderId && $senderId != $property->user_id) {
            Message::create([
                'sender_id'    => $senderId,
                'recipient_id' => $property->user_id,
                'subject'      => 'Enquiry: ' . $property->title,
                'body'         => $request->message,
                'property_id'  => $property->id,
            ]);
        }

        // Always notify property owner
        NotificationService::send(
            $property->user_id,
            '📩 New Property Enquiry',
            "{$request->name} ({$request->email}) enquired about {$property->title}: " . Str::limit($request->message, 100),
            'message',
            '/dashboard/messages'
        );

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Enquiry sent! The owner will contact you shortly.']);
        }
        return back()->with('success', 'Enquiry sent successfully!');
    }
}
