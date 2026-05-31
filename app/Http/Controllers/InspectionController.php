<?php

namespace App\Http\Controllers;

use App\Mail\InspectionBooked;
use App\Models\Inspection;
use App\Models\Property;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InspectionController extends Controller
{
    public function book(Request $request)
    {
        $request->validate([
            'property_id'  => 'required|integer|exists:properties,id',
            'scheduled_at' => 'required|date|after:today',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $userId   = session('user_id');
        $property = Property::findOrFail($request->property_id);

        $inspection = Inspection::create([
            'property_id'   => $property->id,
            'requester_id'  => $userId,
            'agent_id'      => $property->user_id,
            'scheduled_at'  => $request->scheduled_at,
            'notes'         => $request->notes,
            'status'        => 'pending',
        ]);

        // Notify property owner via NotificationService
        NotificationService::send(
            $property->user_id,
            'Inspection Request',
            "Inspection requested for \"{$property->title}\" on " . date('d M Y', strtotime($request->scheduled_at)) . ".",
            'inspection',
            '/dashboard'
        );

        // Send mail to property owner and requester
        try {
            $owner = User::find($property->user_id);
            $requester = User::find($userId);
            $inspection->load('property');
            if ($owner?->email) {
                Mail::to($owner->email)->queue(new InspectionBooked($inspection));
            }
            if ($requester && $requester->id !== $owner?->id && $requester->email) {
                Mail::to($requester->email)->queue(new InspectionBooked($inspection));
            }
        } catch (\Throwable $e) {
            Log::warning('InspectionBooked mail failed: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success'       => true,
                'message'       => 'Inspection booked successfully.',
                'inspection_id' => $inspection->id,
            ]);
        }

        return redirect()->back()->with('success', 'Inspection booked successfully. The owner will confirm shortly.');
    }
}
