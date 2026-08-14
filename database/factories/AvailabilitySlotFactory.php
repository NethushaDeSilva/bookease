<?php

namespace Database\Factories;

use App\Models\AvailabilitySlot;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvailabilitySlot>
 */
class AvailabilitySlotFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = now()
            ->addDays(fake()->numberBetween(1, 45))
            ->setTime(fake()->randomElement([9, 10, 11, 13, 14, 15]), 0);

        $duration = fake()->randomElement([30, 45, 60, 90]);

        return [
            'service_id' => Service::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMinutes($duration),
            'capacity' => fake()->numberBetween(1, 4),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}