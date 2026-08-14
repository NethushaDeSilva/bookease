<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\AvailabilitySlotResource;

class ServiceController extends Controller
{
    /**
     * Display customer-visible services.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'sort' => [
                'nullable',
                'in:name,price_low,price_high,rating,newest',
            ],
            'only_available' => [
                'nullable',
                'boolean',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $search = trim($validated['search'] ?? '');

        $sort = $validated['sort'] ?? 'name';

        $onlyAvailable = $request->boolean(
            'only_available'
        );

        $perPage = (int) ($validated['per_page'] ?? 15);

        $query = Service::query()
            ->visibleToCustomers()
            ->with([
                'business:id,name,slug,address,phone,email',
            ])
            ->withCount([
                'availabilitySlots as available_slots_count' =>
                fn(Builder $query) => $query->available(),
                'reviews',
            ])
            ->withAvg('reviews', 'rating')
            ->search($search)
            ->when(
                $onlyAvailable,
                fn(Builder $query) => $query->whereHas(
                    'availabilitySlots',
                    fn(Builder $query) => $query->available()
                )
            );

        $query = match ($sort) {
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

        return ServiceResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    /**
     * Display one customer-visible service.
     */
    public function show(Service $service): ServiceResource
    {
        $service = Service::query()
            ->visibleToCustomers()
            ->with([
                'business:id,name,slug,address,phone,email',
            ])
            ->withCount([
                'availabilitySlots as available_slots_count' =>
                fn(Builder $query) => $query->available(),
                'reviews',
            ])
            ->withAvg('reviews', 'rating')
            ->findOrFail($service->id);

        return new ServiceResource($service);
    }

    /**
     * Display available slots for a service.
     */
    public function slots(
        Request $request,
        Service $service
    ): AnonymousResourceCollection {
        $validated = $request->validate([
            'date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $service = Service::query()
            ->visibleToCustomers()
            ->findOrFail($service->id);

        $perPage = (int) ($validated['per_page'] ?? 30);

        $slots = $service
            ->availabilitySlots()
            ->available()
            ->withCount('activeBookings')
            ->when(
                isset($validated['date']),
                fn(Builder $query) => $query->whereDate(
                    'starts_at',
                    $validated['date']
                )
            )
            ->orderBy('starts_at')
            ->paginate($perPage)
            ->withQueryString();

        return AvailabilitySlotResource::collection($slots);
    }
}
