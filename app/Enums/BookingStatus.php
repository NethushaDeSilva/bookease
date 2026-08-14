<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';

    public static function activeValues(): array
    {
        return [
            self::Pending->value,
            self::Confirmed->value,
        ];
    }
}