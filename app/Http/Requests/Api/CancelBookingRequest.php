<?php

namespace App\Http\Requests\Api;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;

class CancelBookingRequest extends FormRequest
{
    /**
     * Determine whether this booking can be cancelled.
     */
    public function authorize(): bool
    {
        $booking = $this->route('booking');

        return $booking instanceof Booking
            && ($this->user()?->can(
                'cancel',
                $booking
            ) ?? false);
    }

    /**
     * Validation rules for cancellation.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => [
                'required',
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
                'reason' => trim((string) $this->input('reason')),
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
            'reason.required' =>
                'Please provide a cancellation reason.',

            'reason.min' =>
                'The cancellation reason must contain at least 5 characters.',

            'reason.max' =>
                'The cancellation reason may not contain more than 1000 characters.',
        ];
    }
}