<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('user_id');
    }

    public function rules(): array
    {
        return [
            'property_id'  => 'required|integer|exists:properties,id',
            'scheduled_at' => 'required|date|after:today',
            'notes'        => 'nullable|string|max:1000',
        ];
    }
}
