<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BookingList extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public ?int $cancellingBookingId = null;

    public string $cancellationReason = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
        ]);

        $this->resetPage();
    }

    public function startCancellation(int $bookingId): void
    {
        $booking = $this->customerBooking($bookingId);

        $this->authorize('cancel', $booking);

        $this->cancellingBookingId = $booking->id;
        $this->cancellationReason = '';

        $this->resetValidation();
    }

    public function closeCancellation(): void
    {
        $this->reset([
            'cancellingBookingId',
            'cancellationReason',
        ]);

        $this->resetValidation();
    }

    public function confirmCancellation(): void
    {
        $validated = $this->validate([
            'cancellationReason' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ]);

        $user = $this->authenticatedUser();
        $bookingId = $this->cancellingBookingId;

        abort_unless($bookingId !== null, 404);

        DB::transaction(function () use (
            $bookingId,
            $user,
            $validated
        ): void {
            $booking = Booking::query()
                ->with([
                    'slot',
                    'service.business',
                ])
                ->where('customer_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($bookingId);

            $this->authorize('cancel', $booking);

            $oldStatus = $booking->status;

            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancellation_reason' =>
                    $validated['cancellationReason'],
                'cancelled_at' => now(),
            ]);

            $booking->statusHistories()->create([
                'changed_by' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => BookingStatus::Cancelled,
                'reason' => $validated['cancellationReason'],
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'booking.cancelled',
                'entity_type' => $booking->getMorphClass(),
                'entity_id' => $booking->id,
                'metadata' => [
                    'booking_reference' =>
                        $booking->booking_reference,
                    'old_status' => $oldStatus->value,
                    'new_status' =>
                        BookingStatus::Cancelled->value,
                    'reason' =>
                        $validated['cancellationReason'],
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        $this->closeCancellation();

        session()->flash(
            'success',
            'Your booking was cancelled successfully.'
        );
    }

    private function customerBooking(int $bookingId): Booking
    {
        return Booking::query()
            ->with([
                'slot',
                'service.business',
            ])
            ->where(
                'customer_id',
                $this->authenticatedUser()->id
            )
            ->findOrFail($bookingId);
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): View
    {
        $user = $this->authenticatedUser();

        $this->authorize('viewAny', Booking::class);

        $validStatuses = array_map(
            fn (BookingStatus $status): string => $status->value,
            BookingStatus::cases()
        );

        $search = trim($this->search);

        $bookings = Booking::query()
            ->forCustomer($user)
            ->with([
                'service:id,business_id,name,duration_minutes',
                'service.business:id,name,slug,address,phone',
                'slot:id,starts_at,ends_at',
                'review:id,booking_id,rating',
            ])
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where(
                                    'booking_reference',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'service',
                                    function (Builder $query) use ($search): void {
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                )
                                ->orWhereHas(
                                    'service.business',
                                    function (Builder $query) use ($search): void {
                                        $query->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                in_array(
                    $this->statusFilter,
                    $validStatuses,
                    true
                ),
                fn (Builder $query) => $query->where(
                    'status',
                    $this->statusFilter
                )
            )
            ->latest()
            ->paginate(10);

        return view(
            'livewire.customer.booking-list',
            [
                'bookings' => $bookings,
                'statusOptions' => BookingStatus::cases(),
            ]
        );
    }
}