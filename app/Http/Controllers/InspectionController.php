<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\NotificationLog;
use App\Models\Property;
use Illuminate\Http\Request;

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

        // Notify property owner
        NotificationLog::create([
            'user_id' => $property->user_id,
            'type'    => 'inspection_request',
            'message' => "Inspection requested for \"{$property->title}\" on " . date('d M Y', strtotime($request->scheduled_at)) . ".",
            'data'    => json_encode(['inspection_id' => $inspection->id]),
        ]);

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
