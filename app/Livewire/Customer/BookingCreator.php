<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Models\ActivityLog;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookingCreator extends Component
{
    use AuthorizesRequests;

    public Service $service;

    public AvailabilitySlot $slot;

    public string $notes = '';

    public bool $bookingCreated = false;

    public ?string $createdBookingReference = null;

    public function mount(
        Service $service,
        AvailabilitySlot $slot
    ): void {
        $service->load('business');

        $this->authorize('view', $service);
        $this->authorize('create', Booking::class);

        abort_unless(
            $slot->service_id === $service->id,
            404
        );

        abort_unless($slot->isAvailable(), 404);

        $this->service = $service;
        $this->slot = $slot;
    }

    protected function rules(): array
    {
        return [
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function createBooking(): void
    {
        $validated = $this->validate();
        $user = $this->authenticatedUser();

        $this->authorize('create', Booking::class);

        $booking = DB::transaction(function () use (
            $validated,
            $user
        ): Booking {
            $slot = AvailabilitySlot::query()
                ->with('service.business')
                ->lockForUpdate()
                ->findOrFail($this->slot->id);

            if ($slot->service_id !== $this->service->id) {
                throw ValidationException::withMessages([
                    'slot' => 'The selected appointment does not belong to this service.',
                ]);
            }

            $this->authorize('view', $slot->service);

            if (
                ! $slot->is_active
                || ! $slot->starts_at->isFuture()
            ) {
                throw ValidationException::withMessages([
                    'slot' => 'This appointment is no longer available.',
                ]);
            }

            $activeBookingCount = $slot
                ->bookings()
                ->whereIn(
                    'status',
                    BookingStatus::activeValues()
                )
                ->count();

            if ($activeBookingCount >= $slot->capacity) {
                throw ValidationException::withMessages([
                    'slot' => 'This appointment has just become fully booked.',
                ]);
            }

            $duplicateBookingExists = Booking::query()
                ->where('customer_id', $user->id)
                ->where('slot_id', $slot->id)
                ->whereIn(
                    'status',
                    BookingStatus::activeValues()
                )
                ->exists();

            if ($duplicateBookingExists) {
                throw ValidationException::withMessages([
                    'slot' => 'You already have an active booking for this appointment.',
                ]);
            }

            $notes = trim($validated['notes'] ?? '');

            $booking = Booking::create([
                'booking_reference' => $this->createReference(),
                'customer_id' => $user->id,
                'service_id' => $slot->service_id,
                'slot_id' => $slot->id,
                'status' => BookingStatus::Pending,
                'price' => $slot->service->price,
                'notes' => $notes !== '' ? $notes : null,
            ]);

            $booking->statusHistories()->create([
                'changed_by' => $user->id,
                'old_status' => null,
                'new_status' => BookingStatus::Pending,
                'reason' => 'Booking created by customer.',
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'booking.created',
                'entity_type' => $booking->getMorphClass(),
                'entity_id' => $booking->id,
                'metadata' => [
                    'booking_reference' =>
                        $booking->booking_reference,
                    'service_id' => $booking->service_id,
                    'slot_id' => $booking->slot_id,
                    'status' => $booking->status->value,
                    'price' => $booking->price,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $booking;
        });

        $this->createdBookingReference =
            $booking->booking_reference;

        $this->bookingCreated = true;
        $this->notes = '';

        $this->slot->refresh();
        $this->slot->loadCount('activeBookings');
    }

    private function createReference(): string
    {
        do {
            $reference = 'BE-'
                .now()->format('Ymd')
                .'-'
                .Str::upper(Str::random(8));
        } while (
            Booking::query()
                ->where('booking_reference', $reference)
                ->exists()
        );

        return $reference;
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): View
    {
        $this->service->load('business');
        $this->slot->loadCount('activeBookings');

        return view('livewire.customer.booking-creator');
    }
}