<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilitySlotResource extends JsonResource
{
    /**
     * Transform the slot into an API response.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $activeBookingCount = (int) (
            $this->active_bookings_count ?? 0
        );

        $remainingCapacity = max(
            0,
            $this->capacity - $activeBookingCount
        );

        return [
            'id' => $this->id,

            'service_id' => $this->service_id,

            'starts_at' => $this->starts_at?->toIso8601String(),

            'ends_at' => $this->ends_at?->toIso8601String(),

            'date' => $this->starts_at?->format('Y-m-d'),

            'start_time' => $this->starts_at?->format('H:i'),

            'end_time' => $this->ends_at?->format('H:i'),

            'duration_minutes' => $this->starts_at
                ? (int) $this->starts_at->diffInMinutes(
                    $this->ends_at
                )
                : null,

            'capacity' => $this->capacity,

            'active_bookings_count' => $activeBookingCount,

            'remaining_capacity' => $remainingCapacity,

            'is_active' => $this->is_active,

            'is_available' => $this->is_active
                && $this->starts_at?->isFuture()
                && $remainingCapacity > 0,
        ];
    }
}