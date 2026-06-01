<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'asset_id'          => ['required', 'integer', 'exists:assets,id'],
            'start_date'        => ['required', 'date', 'after:now', 'before:end_date'],
            'end_date'          => ['required', 'date', 'after:start_date'],
            'pickup_location'   => ['nullable', 'string', 'max:255'],
            'dropoff_location'  => ['nullable', 'string', 'max:255'],
            'with_driver'       => ['nullable', 'boolean'],
            'insurance_type'    => ['nullable', 'in:cdw,tpl,none'],
            'notes'             => ['nullable', 'string', 'max:1000'],
            'promo_code'        => ['nullable', 'string', 'max:30'],
            'currency'          => ['nullable', 'string', 'in:KES,UGX,TZS,NGN,GHS,ZAR,USD,GBP,EUR'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.before' => 'Start date must be before end date.',
            'end_date.after'    => 'End date must be after start date.',
            'start_date.after'  => 'Booking must start in the future.',
        ];
    }
}
