<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateListingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'min:5', 'max:120'],
            'description'       => ['required', 'string', 'min:20', 'max:5000'],
            'category'          => ['required', 'string', 'in:sedan,suv,truck,van,bus,motorcycle,heavy_equipment,tractor,crane,excavator,forklift,compactor,generator'],
            'make'              => ['required', 'string', 'max:60'],
            'model'             => ['required', 'string', 'max:60'],
            'year'              => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'daily_rate_kes'    => ['required', 'numeric', 'min:500', 'max:5000000'],
            'deposit_kes'       => ['nullable', 'numeric', 'min:0'],
            'location_city'     => ['required', 'string', 'max:60'],
            'location_country'  => ['required', 'string', 'size:2'],
            'latitude'          => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude'         => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'seats'             => ['nullable', 'integer', 'min:1', 'max:100'],
            'fuel_type'         => ['nullable', 'in:petrol,diesel,electric,hybrid,cng'],
            'transmission'      => ['nullable', 'in:manual,automatic,semi-automatic'],
            'is_for_sale'       => ['nullable', 'boolean'],
            'sale_price_kes'    => ['required_if:is_for_sale,true', 'nullable', 'numeric', 'min:1000'],
            'features'          => ['nullable', 'array'],
            'features.*'        => ['string', 'max:50'],
        ];
    }
}
