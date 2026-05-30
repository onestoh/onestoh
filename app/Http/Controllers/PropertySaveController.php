<?php

namespace App\Http\Controllers;

use App\Models\PropertySave;
use Illuminate\Http\Request;

class PropertySaveController extends Controller
{
    public function toggle(Request $request, $id)
    {
        $userId = session('user_id');

        $existing = PropertySave::where('user_id', $userId)
            ->where('property_id', $id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        PropertySave::create([
            'user_id'     => $userId,
            'property_id' => $id,
        ]);

        return response()->json(['saved' => true]);
    }
}
