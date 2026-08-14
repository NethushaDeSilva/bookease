<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory()->completed(),
            'customer_id' => function (array $attributes) {
                return Booking::query()->findOrFail($attributes['booking_id'])->customer_id;
            },
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional()->paragraph(),
        ];
    }
}