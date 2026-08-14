<?php

namespace App\Livewire\Customer;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ServiceCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sort = 'name';

    #[Url]
    public bool $onlyAvailable = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedOnlyAvailable(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'sort',
            'onlyAvailable',
        ]);

        $this->resetPage();
    }

    public function render(): View
    {
        $query = Service::query()
            ->visibleToCustomers()
            ->with([
                'business:id,name,slug,address',
            ])
            ->withCount([
                'availabilitySlots as available_slots_count' =>
                    fn (Builder $query) => $query->available(),
                'reviews',
            ])
            ->withAvg('reviews', 'rating')
            ->search($this->search)
            ->when(
                $this->onlyAvailable,
                fn (Builder $query) => $query->whereHas(
                    'availabilitySlots',
                    fn (Builder $query) => $query->available()
                )
            );

        $query = match ($this->sort) {
            'price_low' => $query
                ->orderBy('price')
                ->orderBy('name'),

            'price_high' => $query
                ->orderByDesc('price')
                ->orderBy('name'),

            'rating' => $query
                ->orderByDesc('reviews_avg_rating')
                ->orderBy('name'),

            'newest' => $query
                ->latest(),

            default => $query
                ->orderBy('name'),
        };

        return view(
            'livewire.customer.service-catalog',
            [
                'services' => $query->paginate(9),
            ]
        );
    }
}