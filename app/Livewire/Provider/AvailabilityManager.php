<?php

namespace App\Livewire\Provider;

use App\Models\ActivityLog;
use App\Models\AvailabilitySlot;
use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AvailabilityManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public ?int $businessId = null;

    public ?int $slotId = null;

    public string $serviceId = '';

    public string $startsAt = '';

    public string $capacity = '1';

    public bool $isActive = true;

    public bool $showForm = false;

    public string $filterServiceId = '';

    public string $dateFilter = '';

    public function mount(): void
    {
        $this->businessId = $this->authenticatedUser()
            ->business()
            ->value('id');
    }

    protected function rules(): array
    {
        return [
            'serviceId' => [
                'required',
                'integer',
                Rule::exists('services', 'id')
                    ->where(function ($query) {
                        $query
                            ->where('business_id', $this->businessId)
                            ->whereNull('deleted_at');
                    }),
            ],
            'startsAt' => [
                'required',
                'date',
                'after:now',
            ],
            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
            'isActive' => [
                'boolean',
            ],
        ];
    }

    public function updatedFilterServiceId(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    public function startCreate(): void
    {
        $this->authorize('create', AvailabilitySlot::class);

        $firstService = $this->providerBusiness()
            ->services()
            ->orderBy('name')
            ->first();

        if (! $firstService) {
            $this->addError(
                'form',
                'Create at least one service before adding availability.'
            );

            return;
        }

        $this->resetForm();

        $this->serviceId = (string) $firstService->id;
        $this->startsAt = now()
            ->addDay()
            ->setTime(9, 0)
            ->format('Y-m-d\TH:i');

        $this->showForm = true;
    }

    public function edit(int $slotId): void
    {
        $slot = $this->providerSlot($slotId);

        $this->authorize('update', $slot);

        $this->slotId = $slot->id;
        $this->serviceId = (string) $slot->service_id;
        $this->startsAt = $slot->starts_at->format('Y-m-d\TH:i');
        $this->capacity = (string) $slot->capacity;
        $this->isActive = $slot->is_active;
        $this->showForm = true;

        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate();
        $user = $this->authenticatedUser();
        $business = $this->providerBusiness();
        $service = $this->providerService(
            (int) $validated['serviceId']
        );

        $startsAt = Carbon::parse($validated['startsAt']);
        $endsAt = $startsAt
            ->copy()
            ->addMinutes($service->duration_minutes);

        $slot = DB::transaction(function () use (
            $validated,
            $user,
            $business,
            $service,
            $startsAt,
            $endsAt
        ): AvailabilitySlot {
            $overlapExists = AvailabilitySlot::query()
                ->whereHas(
                    'service',
                    function (Builder $query) use ($business): void {
                        $query->where(
                            'business_id',
                            $business->id
                        );
                    }
                )
                ->when(
                    $this->slotId,
                    fn (Builder $query) => $query->where(
                        'id',
                        '!=',
                        $this->slotId
                    )
                )
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->lockForUpdate()
                ->exists();

            if ($overlapExists) {
                throw ValidationException::withMessages([
                    'startsAt' => 'This appointment overlaps another slot in your schedule.',
                ]);
            }

            $data = [
                'service_id' => $service->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'capacity' => (int) $validated['capacity'],
                'is_active' => $validated['isActive'],
            ];

            if ($this->slotId) {
                $slot = $this->providerSlot($this->slotId);

                $this->authorize('update', $slot);

                $activeBookingCount = $slot
                    ->activeBookings()
                    ->count();

                if (
                    (int) $validated['capacity']
                    < $activeBookingCount
                ) {
                    throw ValidationException::withMessages([
                        'capacity' => "Capacity cannot be lower than {$activeBookingCount} active bookings.",
                    ]);
                }

                $slot->update($data);

                $action = 'availability.updated';
            } else {
                $this->authorize(
                    'create',
                    AvailabilitySlot::class
                );

                $slot = AvailabilitySlot::create($data);

                $action = 'availability.created';
            }

            $this->recordActivity(
                $user,
                $slot,
                $action
            );

            return $slot;
        });

        $message = $this->slotId
            ? 'Appointment slot updated successfully.'
            : 'Appointment slot created successfully.';

        $this->resetForm();

        session()->flash('success', $message);
    }

    public function toggleActive(int $slotId): void
    {
        $user = $this->authenticatedUser();
        $slot = $this->providerSlot($slotId);

        $this->authorize('update', $slot);

        DB::transaction(function () use ($user, $slot): void {
            $slot->update([
                'is_active' => ! $slot->is_active,
            ]);

            $this->recordActivity(
                $user,
                $slot,
                $slot->is_active
                    ? 'availability.activated'
                    : 'availability.deactivated'
            );
        });

        session()->flash(
            'success',
            $slot->is_active
                ? 'Appointment slot activated.'
                : 'Appointment slot deactivated.'
        );
    }

    public function delete(int $slotId): void
    {
        $user = $this->authenticatedUser();
        $slot = $this->providerSlot($slotId);

        $this->authorize('delete', $slot);

        if ($slot->bookings()->exists()) {
            $this->addError(
                'delete',
                'A slot with booking records cannot be deleted. Deactivate it instead.'
            );

            return;
        }

        DB::transaction(function () use ($user, $slot): void {
            $this->recordActivity(
                $user,
                $slot,
                'availability.deleted'
            );

            $slot->delete();
        });

        if ($this->slotId === $slotId) {
            $this->resetForm();
        }

        session()->flash(
            'success',
            'Appointment slot deleted successfully.'
        );
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'slotId',
            'serviceId',
            'startsAt',
            'showForm',
        ]);

        $this->capacity = '1';
        $this->isActive = true;

        $this->resetValidation();
    }

    private function providerBusiness(): Business
    {
        $business = $this->authenticatedUser()
            ->business()
            ->first();

        abort_unless(
            $business instanceof Business,
            403,
            'Create a business profile before managing availability.'
        );

        return $business;
    }

    private function providerService(int $serviceId): Service
    {
        return Service::query()
            ->where(
                'business_id',
                $this->providerBusiness()->id
            )
            ->findOrFail($serviceId);
    }

    private function providerSlot(int $slotId): AvailabilitySlot
    {
        $business = $this->providerBusiness();

        return AvailabilitySlot::query()
            ->whereHas(
                'service',
                function (Builder $query) use ($business): void {
                    $query->where(
                        'business_id',
                        $business->id
                    );
                }
            )
            ->findOrFail($slotId);
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    private function recordActivity(
        User $user,
        AvailabilitySlot $slot,
        string $action
    ): void {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'entity_type' => $slot->getMorphClass(),
            'entity_id' => $slot->id,
            'metadata' => [
                'service_id' => $slot->service_id,
                'starts_at' => $slot->starts_at->toIso8601String(),
                'ends_at' => $slot->ends_at->toIso8601String(),
                'capacity' => $slot->capacity,
                'is_active' => $slot->is_active,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function render(): View
    {
        $services = Service::query()
            ->where(
                'business_id',
                $this->businessId ?? 0
            )
            ->orderBy('name')
            ->get();

        $query = AvailabilitySlot::query()
            ->with('service')
            ->withCount([
                'bookings',
                'activeBookings',
            ]);

        if ($this->businessId) {
            $query->whereHas(
                'service',
                function (Builder $query): void {
                    $query->where(
                        'business_id',
                        $this->businessId
                    );
                }
            );
        } else {
            $query->whereRaw('1 = 0');
        }

        $slots = $query
            ->when(
                $this->filterServiceId !== '',
                fn (Builder $query) => $query->where(
                    'service_id',
                    $this->filterServiceId
                )
            )
            ->when(
                $this->dateFilter !== '',
                fn (Builder $query) => $query->whereDate(
                    'starts_at',
                    $this->dateFilter
                )
            )
            ->orderBy('starts_at')
            ->paginate(10);

        return view(
            'livewire.provider.availability-manager',
            [
                'services' => $services,
                'slots' => $slots,
                'hasBusiness' => $this->businessId !== null,
            ]
        );
    }
}