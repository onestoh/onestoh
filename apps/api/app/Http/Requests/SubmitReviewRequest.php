<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'booking_id'    => ['required', 'uuid', 'exists:bookings,id'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['required', 'string', 'min:10', 'max:1000'],
            'aspects'       => ['nullable', 'array'],
            'aspects.cleanliness'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'aspects.value'        => ['nullable', 'integer', 'min:1', 'max:5'],
            'aspects.communication' => ['nullable', 'integer', 'min:1', 'max:5'],
            'aspects.accuracy'     => ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }
}
