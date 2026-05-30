<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\NotificationLog;
use App\Models\Property;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        $userId   = session('user_id');
        $property = Property::findOrFail($request->property_id);

        $maintenance = MaintenanceRequest::create([
            'property_id' => $property->id,
            'tenant_id'   => $userId,
            'title'       => $request->title,
            'description' => $request->description,
            'priority'    => $request->priority,
            'status'      => 'open',
        ]);

        // Notify landlord
        NotificationLog::create([
            'user_id' => $property->user_id,
            'type'    => 'maintenance_request',
            'message' => "New maintenance request: \"{$request->title}\" for {$property->title}. Priority: {$request->priority}.",
            'data'    => json_encode(['maintenance_id' => $maintenance->id]),
        ]);

        return redirect()->back()->with('success', 'Maintenance request submitted. The landlord will be notified.');
    }
}
