<?php

namespace App\Http\Requests\Api;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine whether the user may create a booking.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(
            'create',
            Booking::class
        ) ?? false;
    }

    /**
     * Validation rules for a new booking.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'slot_id' => [
                'required',
                'integer',
                'exists:availability_slots,id',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slot_id.required' =>
                'Please select an appointment slot.',

            'slot_id.exists' =>
                'The selected appointment slot does not exist.',

            'notes.max' =>
                'The notes may not contain more than 1000 characters.',
        ];
    }
}