<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingStatusHistory>
 */
class BookingStatusHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'changed_by' => User::factory(),
            'old_status' => BookingStatus::Pending->value,
            'new_status' => BookingStatus::Confirmed->value,
            'reason' => fake()->optional()->sentence(),
        ];
    }

    public function initial(): static
    {
        return $this->state(fn () => [
            'old_status' => null,
            'new_status' => BookingStatus::Pending->value,
            'reason' => 'Booking created.',
        ]);
    }
}