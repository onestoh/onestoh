<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('user_id');
    }

    public function rules(): array
    {
        $types   = 'house,apartment,villa,bungalow,townhouse,studio,office,commercial,warehouse,industrial,land,farm,bedsitter,mansion,maisonette';
        $listing = 'sale,rent,lease,auction,off_plan';

        return [
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'type'         => "required|in:{$types}",
            'listing_type' => "required|in:{$listing}",
            'price'        => 'required|numeric|min:1',
            'county'       => 'required|string',
            'location'     => 'required|string',
            'bedrooms'     => 'nullable|integer|min:0|max:50',
            'bathrooms'    => 'nullable|integer|min:0|max:50',
            'area_sqft'    => 'nullable|numeric',
            'images'       => 'nullable|array|max:10',
            'images.*'     => 'image|max:5120',
            'amenities'    => 'nullable|array',
        ];
    }
}
