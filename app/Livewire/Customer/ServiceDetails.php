<?php

namespace App\Livewire\Customer;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ServiceDetails extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public Service $service;

    public string $dateFilter = '';

    public function mount(Service $service): void
    {
        $service->load('business');

        $this->authorize('view', $service);

        $this->service = $service;
    }

    protected function rules(): array
    {
        return [
            'dateFilter' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
        ];
    }

    public function updatedDateFilter(): void
    {
        $this->validateOnly('dateFilter');

        $this->resetPage('slotsPage');
    }

    public function clearDateFilter(): void
    {
        $this->dateFilter = '';

        $this->resetValidation('dateFilter');
        $this->resetPage('slotsPage');
    }

    public function render(): View
    {
        $service = Service::query()
            ->with([
                'business:id,owner_id,name,slug,description,phone,email,address,status',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->findOrFail($this->service->id);

        $this->authorize('view', $service);

        $this->service = $service;

        $slots = $service
            ->availabilitySlots()
            ->available()
            ->withCount('activeBookings')
            ->when(
                $this->dateFilter !== '',
                fn (Builder $query) => $query->whereDate(
                    'starts_at',
                    $this->dateFilter
                )
            )
            ->orderBy('starts_at')
            ->paginate(
                perPage: 10,
                pageName: 'slotsPage'
            );

        $reviews = $service
            ->reviews()
            ->with('customer:id,name')
            ->latest()
            ->limit(6)
            ->get();

        return view(
            'livewire.customer.service-details',
            [
                'slots' => $slots,
                'reviews' => $reviews,
            ]
        );
    }
}
