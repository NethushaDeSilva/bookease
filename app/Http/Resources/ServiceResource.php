<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the service into an API response.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'duration_minutes' => $this->duration_minutes,

            'price' => (float) $this->price,

            'is_active' => $this->is_active,

            'available_slots_count' => (int) (
                $this->available_slots_count ?? 0
            ),

            'reviews' => [
                'count' => (int) (
                    $this->reviews_count ?? 0
                ),

                'average_rating' =>
                    $this->reviews_avg_rating !== null
                        ? round(
                            (float) $this->reviews_avg_rating,
                            1
                        )
                        : null,
            ],

            'business' => $this->whenLoaded(
                'business',
                fn (): array => [
                    'id' => $this->business->id,
                    'name' => $this->business->name,
                    'slug' => $this->business->slug,
                    'address' => $this->business->address,
                    'phone' => $this->business->phone,
                    'email' => $this->business->email,
                ]
            ),

            'created_at' => $this->created_at?->toIso8601String(),

            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}