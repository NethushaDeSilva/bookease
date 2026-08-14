<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_reference' => 'BKE-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
            'customer_id' => User::factory()->customer(),
            'service_id' => Service::factory(),
            'slot_id' => function (array $attributes) {
                return AvailabilitySlot::factory()->create([
                    'service_id' => $attributes['service_id'],
                ])->id;
            },
            'status' => BookingStatus::Pending->value,
            'price' => function (array $attributes) {
                return Service::query()->findOrFail($attributes['service_id'])->price;
            },
            'notes' => fake()->optional()->sentence(),
            'cancellation_reason' => null,
            'cancelled_at' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Confirmed->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Completed->value,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Cancelled->value,
            'cancellation_reason' => fake()->sentence(),
            'cancelled_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Rejected->value,
        ]);
    }
}