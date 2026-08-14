<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the booking into an API response.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'booking_reference' => $this->booking_reference,

            'status' => $this->status->value,

            'price' => (float) $this->price,

            'notes' => $this->notes,

            'cancellation_reason' =>
                $this->cancellation_reason,

            'cancelled_at' =>
                $this->cancelled_at?->toIso8601String(),

            'customer' => $this->whenLoaded(
                'customer',
                fn (): array => [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                ]
            ),

            'service' => $this->whenLoaded(
                'service',
                function (): array {
                    $service = [
                        'id' => $this->service->id,
                        'name' => $this->service->name,
                        'duration_minutes' =>
                            $this->service->duration_minutes,
                    ];

                    if (
                        $this->service->relationLoaded('business')
                    ) {
                        $service['business'] = [
                            'id' => $this->service->business->id,
                            'name' =>
                                $this->service->business->name,
                            'address' =>
                                $this->service->business->address,
                        ];
                    }

                    return $service;
                }
            ),

            'slot' => $this->whenLoaded(
                'slot',
                fn (): array => [
                    'id' => $this->slot->id,
                    'starts_at' =>
                        $this->slot->starts_at?->toIso8601String(),
                    'ends_at' =>
                        $this->slot->ends_at?->toIso8601String(),
                ]
            ),

            'created_at' =>
                $this->created_at?->toIso8601String(),

            'updated_at' =>
                $this->updated_at?->toIso8601String(),
        ];
    }
}