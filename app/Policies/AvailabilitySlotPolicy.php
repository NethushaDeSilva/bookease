<?php

namespace App\Policies;

use App\Models\AvailabilitySlot;
use App\Models\User;

class AvailabilitySlotPolicy
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
        return $user->isCustomer()
            || $user->isProvider();
    }

    public function view(
        User $user,
        AvailabilitySlot $availabilitySlot
    ): bool {
        if ($this->providerOwnsSlot($user, $availabilitySlot)) {
            return true;
        }

        return $user->isCustomer()
            && $availabilitySlot->is_active
            && $availabilitySlot->starts_at->isFuture()
            && $availabilitySlot->service->is_active
            && $availabilitySlot->service->business->isActive();
    }

    public function create(User $user): bool
    {
        return $user->isProvider()
            && $user->business()->exists();
    }

    public function update(
        User $user,
        AvailabilitySlot $availabilitySlot
    ): bool {
        return $this->providerOwnsSlot($user, $availabilitySlot);
    }

    public function delete(
        User $user,
        AvailabilitySlot $availabilitySlot
    ): bool {
        return $this->providerOwnsSlot($user, $availabilitySlot);
    }

    private function providerOwnsSlot(
        User $user,
        AvailabilitySlot $availabilitySlot
    ): bool {
        return $user->isProvider()
            && $availabilitySlot
                ->service
                ->business
                ->owner_id === $user->id;
    }
}