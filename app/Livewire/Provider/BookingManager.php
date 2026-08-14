<?php

namespace App\Livewire\Provider;

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
class BookingManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public ?int $businessId = null;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public string $dateFilter = '';

    public ?int $selectedBookingId = null;

    public string $actionType = '';

    public string $reason = '';

    public function mount(): void
    {
        $this->businessId = $this->authenticatedUser()
            ->business()
            ->value('id');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->validateOnly('dateFilter', [
            'dateFilter' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
            'dateFilter',
        ]);

        $this->resetPage();
    }

    public function confirmBooking(int $bookingId): void
    {
        $this->transitionBooking(
            $bookingId,
            'confirm',
            BookingStatus::Confirmed,
            'Booking confirmed by provider.'
        );

        session()->flash(
            'success',
            'Booking confirmed successfully.'
        );
    }

    public function completeBooking(int $bookingId): void
    {
        $this->transitionBooking(
            $bookingId,
            'complete',
            BookingStatus::Completed,
            'Service marked as completed by provider.'
        );

        session()->flash(
            'success',
            'Booking marked as completed.'
        );
    }

    public function openReasonForm(
        int $bookingId,
        string $action
    ): void {
        abort_unless(
            in_array($action, ['reject', 'cancel'], true),
            422
        );

        $booking = $this->providerBooking($bookingId);

        $this->authorize($action, $booking);

        $this->selectedBookingId = $booking->id;
        $this->actionType = $action;
        $this->reason = '';

        $this->resetValidation();
    }

    public function closeReasonForm(): void
    {
        $this->reset([
            'selectedBookingId',
            'actionType',
            'reason',
        ]);

        $this->resetValidation();
    }

    public function submitReasonAction(): void
    {
        $validated = $this->validate([
            'reason' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ]);

        abort_unless(
            $this->selectedBookingId !== null,
            404
        );

        [$ability, $newStatus, $successMessage] = match (
            $this->actionType
        ) {
            'reject' => [
                'reject',
                BookingStatus::Rejected,
                'Booking rejected successfully.',
            ],
            'cancel' => [
                'cancel',
                BookingStatus::Cancelled,
                'Booking cancelled successfully.',
            ],
            default => abort(422),
        };

        $this->transitionBooking(
            $this->selectedBookingId,
            $ability,
            $newStatus,
            $validated['reason']
        );

        $this->closeReasonForm();

        session()->flash('success', $successMessage);
    }

    private function transitionBooking(
        int $bookingId,
        string $ability,
        BookingStatus $newStatus,
        string $reason
    ): Booking {
        $user = $this->authenticatedUser();

        return DB::transaction(function () use (
            $bookingId,
            $ability,
            $newStatus,
            $reason,
            $user
        ): Booking {
            $booking = $this->providerBookingsQuery($user)
                ->with([
                    'slot',
                    'service.business',
                ])
                ->lockForUpdate()
                ->findOrFail($bookingId);

            $this->authorize($ability, $booking);

            $oldStatus = $booking->status;

            $updateData = [
                'status' => $newStatus,
            ];

            if ($newStatus === BookingStatus::Cancelled) {
                $updateData['cancellation_reason'] = $reason;
                $updateData['cancelled_at'] = now();
            }

            $booking->update($updateData);

            $booking->statusHistories()->create([
                'changed_by' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $reason,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => "booking.{$newStatus->value}",
                'entity_type' => $booking->getMorphClass(),
                'entity_id' => $booking->id,
                'metadata' => [
                    'booking_reference' =>
                        $booking->booking_reference,
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'reason' => $reason,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $booking;
        });
    }

    private function providerBooking(int $bookingId): Booking
    {
        return $this->providerBookingsQuery(
            $this->authenticatedUser()
        )
            ->with([
                'slot',
                'service.business',
            ])
            ->findOrFail($bookingId);
    }

    private function providerBookingsQuery(User $user): Builder
    {
        return Booking::query()
            ->whereHas(
                'service.business',
                function (Builder $query) use ($user): void {
                    $query->where('owner_id', $user->id);
                }
            );
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

        $query = $this->providerBookingsQuery($user)
            ->with([
                'customer:id,name,email',
                'service:id,business_id,name,duration_minutes',
                'service.business:id,name,owner_id,address',
                'slot:id,starts_at,ends_at',
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
                                    'customer',
                                    function (Builder $query) use ($search): void {
                                        $query
                                            ->where(
                                                'name',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
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
            ->when(
                $this->dateFilter !== '',
                fn (Builder $query) => $query->whereHas(
                    'slot',
                    fn (Builder $query) => $query->whereDate(
                        'starts_at',
                        $this->dateFilter
                    )
                )
            )
            ->latest();

        if (! $this->businessId) {
            $query->whereRaw('1 = 0');
        }

        return view(
            'livewire.provider.booking-manager',
            [
                'bookings' => $query->paginate(10),
                'statusOptions' => BookingStatus::cases(),
                'hasBusiness' => $this->businessId !== null,
            ]
        );
    }
}