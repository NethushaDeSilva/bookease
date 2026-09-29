<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if (! $user->isActive()) {
            return false;
        }

        $adminAbilities = [
            'viewAny',
            'view',
            'update',
            'confirm',
            'reject',
            'complete',
            'cancel',
            'reschedule',
        ];

        if (
            $user->isAdmin()
            && in_array($ability, $adminAbilities, true)
        ) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isCustomer()
            || $user->isProvider();
    }

    public function view(User $user, Booking $booking): bool
    {
        return $booking->customer_id === $user->id
            || $this->providerOwnsBooking($user, $booking);
    }

    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->providerOwnsBooking($user, $booking);
    }

    public function confirm(User $user, Booking $booking): bool
    {
        return $this->providerOwnsBooking($user, $booking)
            && $booking->status === BookingStatus::Pending;
    }

    public function reject(User $user, Booking $booking): bool
    {
        return $this->providerOwnsBooking($user, $booking)
            && $booking->status === BookingStatus::Pending;
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $this->providerOwnsBooking($user, $booking)
            && $booking->status === BookingStatus::Confirmed;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($booking->customer_id === $user->id) {
            return $booking->canBeCancelledByCustomer();
        }

        return $this->providerOwnsBooking($user, $booking)
            && in_array($booking->status, [
                BookingStatus::Pending,
                BookingStatus::Confirmed,
            ], true);
    }

    public function reschedule(User $user, Booking $booking): bool
    {
        return $this->providerOwnsBooking($user, $booking)
            && in_array($booking->status, [
                BookingStatus::Pending,
                BookingStatus::Confirmed,
            ], true);
    }

    public function review(User $user, Booking $booking): bool
    {
        return ($user->isCustomer() || $user->isProvider())
            && $booking->customer_id === $user->id
            && $booking->status === BookingStatus::Completed
            && $booking->review()->doesntExist();
    }

    public function delete(User $user, Booking $booking): bool
    {
        return false;
    }

    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }

    private function providerOwnsBooking(
        User $user,
        Booking $booking
    ): bool {
        return $user->isProvider()
            && $booking->service->business->owner_id === $user->id;
    }
}
