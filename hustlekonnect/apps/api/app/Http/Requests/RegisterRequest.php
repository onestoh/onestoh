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
            'name'     => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-\'\.']+$/u'],
            'email'    => ['required', 'email:rfc,dns', 'max:150', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
            'role'     => ['required', 'in:client,owner,broker'],
            'country'  => ['nullable', 'string', 'size:2', 'in:KE,UG,TZ,NG,GH,ZA,RW,ET'],
            'referral_code' => ['nullable', 'string', 'max:20'],
            'business_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'  => 'Name may only contain letters, spaces, hyphens, and apostrophes.',
            'phone.regex' => 'Please enter a valid phone number.',
        ];
    }
}
