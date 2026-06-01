<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'booking_id'   => ['required', 'uuid', 'exists:bookings,id'],
            'phone_number' => ['nullable', 'string', 'regex:/^\+?[0-9]{9,15}$/'],
            'gateway'      => ['required', 'in:mpesa,mtn_momo,flutterwave,stripe,paystack,wallet'],
            'currency'     => ['nullable', 'string', 'in:KES,UGX,TZS,NGN,GHS,ZAR,USD,GBP,EUR'],
        ];
    }
}
