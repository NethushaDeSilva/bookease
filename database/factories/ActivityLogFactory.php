<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement([
                'business.created',
                'service.updated',
                'booking.created',
                'booking.confirmed',
                'booking.cancelled',
                'user.suspended',
            ]),
            'entity_type' => null,
            'entity_id' => null,
            'metadata' => [
                'source' => 'factory',
            ],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}