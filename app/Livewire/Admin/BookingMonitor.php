<?php

namespace App\Livewire\Admin;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Business;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BookingMonitor extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'business')]
    public string $businessFilter = '';

    #[Url(as: 'period')]
    public string $periodFilter = '';

    public string $dateFilter = '';

    public function mount(): void
    {
        $this->adminUser();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBusinessFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPeriodFilter(): void
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
            'businessFilter',
            'periodFilter',
            'dateFilter',
        ]);

        $this->resetValidation();
        $this->resetPage();
    }

    private function adminUser(): User
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User && $user->isAdmin(),
            403
        );

        return $user;
    }

    public function render(): View
    {
        $this->adminUser();
        $this->authorize('viewAny', Booking::class);

        $validStatuses = array_map(
            fn (BookingStatus $status): string => $status->value,
            BookingStatus::cases()
        );

        $validPeriods = [
            'upcoming',
            'past',
        ];

        $search = trim($this->search);

        $businessId = ctype_digit($this->businessFilter)
            ? (int) $this->businessFilter
            : null;

        $bookings = Booking::query()
            ->with([
                'customer:id,name,email',
                'service:id,business_id,name,duration_minutes',
                'service.business:id,owner_id,name,address',
                'service.business.owner:id,name,email',
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
                                )
                                ->orWhereHas(
                                    'service.business.owner',
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
                $businessId !== null,
                fn (Builder $query) => $query->whereHas(
                    'service',
                    fn (Builder $query) => $query->where(
                        'business_id',
                        $businessId
                    )
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
            ->when(
                in_array(
                    $this->periodFilter,
                    $validPeriods,
                    true
                ),
                function (Builder $query): void {
                    if ($this->periodFilter === 'upcoming') {
                        $query->whereHas(
                            'slot',
                            fn (Builder $query) => $query->where(
                                'starts_at',
                                '>',
                                now()
                            )
                        );
                    }

                    if ($this->periodFilter === 'past') {
                        $query->whereHas(
                            'slot',
                            fn (Builder $query) => $query->where(
                                'ends_at',
                                '<',
                                now()
                            )
                        );
                    }
                }
            )
            ->latest()
            ->paginate(15);

        $storedStatusCounts = Booking::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusCounts = collect(BookingStatus::cases())
            ->mapWithKeys(
                fn (BookingStatus $status): array => [
                    $status->value => (int) (
                        $storedStatusCounts[$status->value] ?? 0
                    ),
                ]
            )
            ->all();

        $metrics = [
            'total' => Booking::query()->count(),

            'today' => Booking::query()
                ->whereHas(
                    'slot',
                    fn (Builder $query) => $query->whereDate(
                        'starts_at',
                        today()
                    )
                )
                ->count(),

            'upcoming_active' => Booking::query()
                ->whereIn(
                    'status',
                    BookingStatus::activeValues()
                )
                ->upcoming()
                ->count(),

            'completed_value' => (float) Booking::query()
                ->where(
                    'status',
                    BookingStatus::Completed->value
                )
                ->sum('price'),
        ];

        return view(
            'livewire.admin.booking-monitor',
            [
                'bookings' => $bookings,
                'businesses' => Business::query()
                    ->orderBy('name')
                    ->get(['id', 'name']),
                'statusOptions' => BookingStatus::cases(),
                'statusCounts' => $statusCounts,
                'metrics' => $metrics,
            ]
        );
    }
}