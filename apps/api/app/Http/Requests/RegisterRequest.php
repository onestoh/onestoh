<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-\'\. ]+$/u'],
            'email'                 => ['required', 'email:rfc,dns', 'max:150', 'unique:users,email'],
            'phone'                 => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'password'              => ['required', 'confirmed', Password::min(8)->letters()->numbers()->symbols()->uncompromised()],
            'role'                  => ['required', 'in:client,owner,broker'],
            'country'               => ['nullable', 'string', 'size:2', 'in:KE,UG,TZ,NG,GH,ZA,RW,ET'],
            'business_name'         => ['nullable', 'string', 'max:100'],
            'referral_code'         => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'        => 'Name may only contain letters, spaces, hyphens, apostrophes, and dots.',
            'phone.regex'       => 'Please enter a valid phone number.',
            'password.uncompromised' => 'This password has appeared in a data breach. Please choose a different password.',
        ];
    }
}
