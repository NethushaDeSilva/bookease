<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, Business $business): bool
    {
        if ($business->isActive()) {
            return true;
        }

        return $user->isProvider()
            && $business->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isProvider()
            && ! $user->business()
                ->withTrashed()
                ->exists();
    }

    public function update(User $user, Business $business): bool
    {
        return $user->isProvider()
            && $business->owner_id === $user->id;
    }

    public function delete(User $user, Business $business): bool
    {
        return $user->isProvider()
            && $business->owner_id === $user->id;
    }

    public function restore(User $user, Business $business): bool
    {
        return $user->isProvider()
            && $business->owner_id === $user->id;
    }

    public function forceDelete(User $user, Business $business): bool
    {
        return false;
    }
}