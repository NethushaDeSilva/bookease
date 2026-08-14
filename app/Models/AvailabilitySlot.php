<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvailabilitySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'starts_at',
        'ends_at',
        'capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'slot_id');
    }

    public function activeBookings(): HasMany
    {
        return $this->bookings()
            ->whereIn('status', BookingStatus::activeValues());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->active()
            ->upcoming()
            ->whereRaw(
                '(SELECT COUNT(*) FROM bookings
                  WHERE bookings.slot_id = availability_slots.id
                  AND bookings.status IN (?, ?)) < availability_slots.capacity',
                BookingStatus::activeValues()
            );
    }

    public function isAvailable(): bool
    {
        return $this->is_active
            && $this->starts_at->isFuture()
            && $this->activeBookings()->count() < $this->capacity;
    }
}