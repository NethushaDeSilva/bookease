<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'customer_id',
        'service_id',
        'slot_id',
        'status',
        'price',
        'notes',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'price' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySlot::class, 'slot_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function scopeForCustomer(Builder $query, User|int $customer): Builder
    {
        $customerId = $customer instanceof User ? $customer->id : $customer;

        return $query->where('customer_id', $customerId);
    }

    public function scopeWithStatus(Builder $query, BookingStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereHas('slot', function (Builder $query): void {
            $query->where('starts_at', '>', now());
        });
    }

    public function canBeCancelledByCustomer(): bool
    {
        return in_array($this->status, [
            BookingStatus::Pending,
            BookingStatus::Confirmed,
        ], true)
            && $this->slot !== null
            && $this->slot->starts_at->greaterThan(now()->addHours(24));
    }
}