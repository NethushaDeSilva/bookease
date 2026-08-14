<?php

namespace Database\Factories;

use App\Enums\BusinessStatus;
use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'owner_id' => User::factory()->provider(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'description' => fake()->paragraph(),
            'phone' => fake()->numerify('07########'),
            'email' => fake()->unique()->companyEmail(),
            'address' => fake()->address(),
            'status' => BusinessStatus::Active->value,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => BusinessStatus::Pending->value,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => BusinessStatus::Suspended->value,
        ]);
    }
}