<?php

namespace App\Http\Requests\Api\Provider;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingStatusRequest extends FormRequest
{
    /**
     * Determine whether the provider owns this booking.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $booking = $this->route('booking');

        return $user instanceof User
            && $user->isProvider()
            && $booking instanceof Booking
            && $user->can('update', $booking);
    }

    /**
     * Validation rules for a status change.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    'confirmed',
                    'rejected',
                    'completed',
                    'cancelled',
                ]),
            ],

            'reason' => [
                'nullable',
                'required_if:status,rejected,cancelled',
                'string',
                'min:5',
                'max:1000',
            ],
        ];
    }

    /**
     * Remove unnecessary spaces before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('reason')) {
            $this->merge([
                'reason' => trim(
                    (string) $this->input('reason')
                ),
            ]);
        }
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' =>
                'Please provide the new booking status.',

            'status.in' =>
                'The selected booking status is not supported.',

            'reason.required_if' =>
                'A reason is required when rejecting or cancelling a booking.',

            'reason.min' =>
                'The reason must contain at least 5 characters.',

            'reason.max' =>
                'The reason may not contain more than 1000 characters.',
        ];
    }
}