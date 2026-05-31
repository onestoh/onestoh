<?php

namespace App\Http\Controllers;

use App\Models\HotelRoom;
use App\Models\Property;
use App\Services\BookingService;
use Illuminate\Http\Request;

class HotelRoomController extends Controller
{
    /**
     * GET /properties/{propertyId}/rooms
     */
    public function index($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $rooms    = HotelRoom::where('property_id', $propertyId)->get();
        return response()->json($rooms);
    }

    /**
     * POST /properties/{propertyId}/rooms
     */
    public function store(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $userId   = session('user_id');

        if ($property->user_id != $userId) {
            abort(403);
        }

        $request->validate([
            'room_number'    => 'required|string|max:20',
            'room_type'      => 'required|in:standard,deluxe,suite,penthouse,family,single,double,twin',
            'name'           => 'required|string|max:100',
            'price_per_night'=> 'required|numeric|min:0',
            'capacity'       => 'required|integer|min:1',
            'amenities'      => 'nullable|array',
            'description'    => 'nullable|string|max:1000',
            'floor'          => 'nullable|integer',
        ]);

        $room = HotelRoom::create([
            'property_id'    => $propertyId,
            'room_number'    => $request->room_number,
            'room_type'      => $request->room_type,
            'name'           => $request->name,
            'description'    => $request->description,
            'floor'          => $request->floor,
            'capacity'       => $request->capacity,
            'price_per_night'=> $request->price_per_night,
            'amenities'      => $request->amenities ?? [],
            'is_active'      => true,
        ]);

        if (request()->expectsJson()) {
            return response()->json($room, 201);
        }
        return redirect()->back()->with('success', 'Room created successfully.');
    }

    /**
     * PUT /properties/{propertyId}/rooms/{roomId}
     */
    public function update(Request $request, $propertyId, $roomId)
    {
        $userId = session('user_id');
        $room   = HotelRoom::where('property_id', $propertyId)->findOrFail($roomId);

        if ($room->property->user_id != $userId) {
            abort(403);
        }

        $room->update($request->only([
            'room_number', 'room_type', 'name', 'description', 'floor',
            'capacity', 'price_per_night', 'amenities', 'is_active'
        ]));

        if ($request->expectsJson()) {
            return response()->json($room);
        }
        return redirect()->back()->with('success', 'Room updated.');
    }

    /**
     * DELETE /properties/{propertyId}/rooms/{roomId}
     */
    public function destroy($propertyId, $roomId)
    {
        $userId = session('user_id');
        $room   = HotelRoom::where('property_id', $propertyId)->findOrFail($roomId);

        if ($room->property->user_id != $userId) {
            abort(403);
        }

        $room->update(['is_active' => false]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Room deactivated.');
    }

    /**
     * GET/POST /properties/{propertyId}/rooms/{roomId}/availability
     */
    public function checkAvailability(Request $request, $roomId)
    {
        $request->validate([
            'check_in'  => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $room      = HotelRoom::findOrFail($roomId);
        $available = BookingService::isAvailable($room->property_id, $request->check_in, $request->check_out, $roomId);

        return response()->json(['available' => $available]);
    }
}
