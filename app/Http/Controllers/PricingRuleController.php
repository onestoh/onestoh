<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyPricingRule;
use Illuminate\Http\Request;

class PricingRuleController extends Controller
{
    /**
     * GET /properties/{propertyId}/pricing
     */
    public function index($propertyId)
    {
        $rules = PropertyPricingRule::where('property_id', $propertyId)
            ->orderByDesc('priority')
            ->get();

        return response()->json($rules);
    }

    /**
     * POST /properties/{propertyId}/pricing
     */
    public function store(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $userId   = session('user_id');

        if ($property->user_id != $userId) {
            abort(403);
        }

        $request->validate([
            'price_per_night'  => 'required|numeric|min:0',
            'minimum_nights'   => 'nullable|integer|min:1',
            'priority'         => 'nullable|integer|min:0',
            'label'            => 'nullable|string|max:80',
            'day_of_week'      => 'nullable|integer|min:0|max:6',
            'date_from'        => 'nullable|date',
            'date_to'          => 'nullable|date|after_or_equal:date_from',
            'room_id'          => 'nullable|integer|exists:hotel_rooms,id',
        ]);

        $rule = PropertyPricingRule::create([
            'property_id'    => $propertyId,
            'room_id'        => $request->room_id,
            'day_of_week'    => $request->day_of_week,
            'date_from'      => $request->date_from,
            'date_to'        => $request->date_to,
            'price_per_night'=> $request->price_per_night,
            'minimum_nights' => $request->minimum_nights ?? 1,
            'priority'       => $request->priority ?? 0,
            'label'          => $request->label,
        ]);

        if ($request->expectsJson()) {
            return response()->json($rule, 201);
        }
        return redirect()->back()->with('success', 'Pricing rule added.');
    }

    /**
     * DELETE /properties/{propertyId}/pricing/{ruleId}
     */
    public function destroy($propertyId, $ruleId)
    {
        $userId = session('user_id');
        $rule   = PropertyPricingRule::where('property_id', $propertyId)->findOrFail($ruleId);
        $prop   = Property::find($propertyId);

        if ($prop && $prop->user_id != $userId) {
            abort(403);
        }

        $rule->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Pricing rule removed.');
    }
}
