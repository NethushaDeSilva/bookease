<?php

namespace App\Livewire\Provider;

use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ServiceManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Locked]
    public ?int $businessId = null;

    public ?int $serviceId = null;

    public string $search = '';

    public string $name = '';

    public string $description = '';

    public string $durationMinutes = '60';

    public string $price = '';

    public bool $isActive = true;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->businessId = $this->authenticatedUser()
            ->business()
            ->value('id');
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'durationMinutes' => [
                'required',
                'integer',
                'min:15',
                'max:1440',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'isActive' => [
                'boolean',
            ],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function startCreate(): void
    {
        $this->authorize('create', Service::class);

        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $serviceId): void
    {
        $service = $this->providerService($serviceId);

        $this->authorize('update', $service);

        $this->serviceId = $service->id;
        $this->name = $service->name;
        $this->description = $service->description ?? '';
        $this->durationMinutes = (string) $service->duration_minutes;
        $this->price = (string) $service->price;
        $this->isActive = $service->is_active;
        $this->showForm = true;

        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate();
        $user = $this->authenticatedUser();
        $business = $this->providerBusiness();

        $service = DB::transaction(function () use (
            $validated,
            $user,
            $business
        ): Service {
            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?: null,
                'duration_minutes' => (int) $validated['durationMinutes'],
                'price' => round((float) $validated['price'], 2),
                'is_active' => $validated['isActive'],
            ];

            if ($this->serviceId) {
                $service = $this->providerService($this->serviceId);

                $this->authorize('update', $service);

                $service->update($data);

                $action = 'service.updated';
            } else {
                $this->authorize('create', Service::class);

                $service = $business->services()->create($data);

                $action = 'service.created';
            }

            $this->recordActivity($user, $service, $action);

            return $service;
        });

        $message = $this->serviceId
            ? 'Service updated successfully.'
            : 'Service created successfully.';

        $this->resetForm();

        session()->flash('success', $message);
    }

    public function toggleActive(int $serviceId): void
    {
        $user = $this->authenticatedUser();
        $service = $this->providerService($serviceId);

        $this->authorize('update', $service);

        DB::transaction(function () use ($user, $service): void {
            $service->update([
                'is_active' => ! $service->is_active,
            ]);

            $this->recordActivity(
                $user,
                $service,
                $service->is_active
                    ? 'service.activated'
                    : 'service.deactivated'
            );
        });

        session()->flash(
            'success',
            $service->is_active
                ? 'Service activated successfully.'
                : 'Service deactivated successfully.'
        );
    }

    public function delete(int $serviceId): void
    {
        $user = $this->authenticatedUser();
        $service = $this->providerService($serviceId);

        $this->authorize('delete', $service);

        if ($service->bookings()->exists()) {
            $this->addError(
                'delete',
                'A service with booking records cannot be deleted. Deactivate it instead.'
            );

            return;
        }

        DB::transaction(function () use ($user, $service): void {
            $this->recordActivity(
                $user,
                $service,
                'service.deleted'
            );

            $service->delete();
        });

        if ($this->serviceId === $serviceId) {
            $this->resetForm();
        }

        session()->flash('success', 'Service deleted successfully.');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'serviceId',
            'name',
            'description',
            'price',
            'showForm',
        ]);

        $this->durationMinutes = '60';
        $this->isActive = true;

        $this->resetValidation();
    }

    private function providerService(int $serviceId): Service
    {
        return Service::query()
            ->where('business_id', $this->providerBusiness()->id)
            ->findOrFail($serviceId);
    }

    private function providerBusiness(): Business
    {
        $business = $this->authenticatedUser()
            ->business()
            ->first();

        abort_unless(
            $business instanceof Business,
            403,
            'Create a business profile before managing services.'
        );

        return $business;
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    private function recordActivity(
        User $user,
        Service $service,
        string $action
    ): void {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'entity_type' => $service->getMorphClass(),
            'entity_id' => $service->id,
            'metadata' => [
                'service_name' => $service->name,
                'business_id' => $service->business_id,
                'is_active' => $service->is_active,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function render(): View
    {
        $query = Service::query()
            ->withCount([
                'availabilitySlots',
                'bookings',
            ]);

        if ($this->businessId) {
            $query->where('business_id', $this->businessId);
        } else {
            $query->whereRaw('1 = 0');
        }

        $services = $query
            ->search($this->search)
            ->latest()
            ->paginate(10);

        return view('livewire.provider.service-manager', [
            'services' => $services,
            'hasBusiness' => $this->businessId !== null,
        ]);
    }
}