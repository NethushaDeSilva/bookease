<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Customer->value,
            'status' => UserStatus::Active->value,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ];
    }

    public function customer(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Customer->value,
        ]);
    }

    public function provider(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Provider->value,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Admin->value,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => UserStatus::Suspended->value,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}