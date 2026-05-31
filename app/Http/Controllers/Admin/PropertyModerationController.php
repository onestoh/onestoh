<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\NotificationService;

class PropertyModerationController extends Controller
{
    public function index()
    {
        $properties = Property::with('owner:id,name,email')
            ->whereIn('status', ['pending', 'suspended'])
            ->latest()
            ->paginate(20);

        return view('admin.properties.index', compact('properties'));
    }

    public function approve($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'active']);

        try {
            NotificationService::send(
                $property->user_id,
                'Property Approved',
                "Your listing \"{$property->title}\" has been approved and is now live.",
                'system',
                '/marketplace/listing/' . $property->id
            );
        } catch (\Throwable $e) {}

        return redirect()->route('admin.properties.index')->with('success', 'Property approved.');
    }

    public function suspend($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'suspended']);

        return redirect()->route('admin.properties.index')->with('error', 'Property suspended.');
    }

    public function feature($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['is_featured' => !$property->is_featured]);

        $msg = $property->is_featured ? 'Property featured.' : 'Property unfeatured.';
        return redirect()->route('admin.properties.index')->with('success', $msg);
    }
}
