<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email:rfc', 'max:150'],
            'password' => ['required', 'string', 'min:1', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
