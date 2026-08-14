<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
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

    public function view(User $user, Service $service): bool
    {
        if (
            $service->is_active
            && $service->business->isActive()
        ) {
            return true;
        }

        return $this->providerOwnsService($user, $service);
    }

    public function create(User $user): bool
    {
        return $user->isProvider()
            && $user->business()->exists();
    }

    public function update(User $user, Service $service): bool
    {
        return $this->providerOwnsService($user, $service);
    }

    public function delete(User $user, Service $service): bool
    {
        return $this->providerOwnsService($user, $service);
    }

    public function restore(User $user, Service $service): bool
    {
        return $this->providerOwnsService($user, $service);
    }

    public function forceDelete(User $user, Service $service): bool
    {
        return false;
    }

    private function providerOwnsService(
        User $user,
        Service $service
    ): bool {
        return $user->isProvider()
            && $service->business->owner_id === $user->id;
    }
}