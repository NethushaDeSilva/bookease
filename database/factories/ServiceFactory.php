<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->randomElement([
                'Private Tutoring',
                'Photography Session',
                'Fitness Training',
                'Device Repair',
                'Home Cleaning',
                'Business Consultation',
            ]),
            'description' => fake()->paragraph(),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120]),
            'price' => fake()->randomFloat(2, 1000, 15000),
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