# BookEase filter code: exact source excerpts

Snapshot: 30 September 2026. These excerpts were copied directly from this checkout. Full files remain authoritative after later changes. Read [the learning guide, sections 6?8](bookease-learning-guide.md#6-what-are-the-filter-files) first. This appendix is a lookup reference, not material to memorize.

Each component excerpt includes its state declarations and complete `render()` method. Imports, action methods and lifecycle hooks are omitted; open the linked file for them. State includes form fields as well as filters: the learning guide explains the difference.

## Administrator businesses

[Full PHP file](../app/Livewire/Admin/BusinessManager.php) ? render starts at line 236. [UI bindings](../resources/views/livewire/admin/business-manager.blade.php).

### Component state (excerpt)

```php
class BusinessManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public ?int $selectedBusinessId = null;

    public string $actionType = '';

    public string $reason = '';
```

### Result query and view

```php
    public function render(): View
    {
        $this->adminUser();

        $validStatuses = array_map(
            fn (BusinessStatus $status): string => $status->value,
            BusinessStatus::cases()
        );

        $search = trim($this->search);

        $businesses = Business::query()
            ->with('owner:id,name,email,status')
            ->withCount([
                'services',
                'services as active_services_count' =>
                    fn (Builder $query) => $query->where(
                        'is_active',
                        true
                    ),
            ])
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
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
                                )
                                ->orWhere(
                                    'phone',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'owner',
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
            ->orderByRaw(
                "CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'active' THEN 2
                    WHEN 'suspended' THEN 3
                    ELSE 4
                END"
            )
            ->latest()
            ->paginate(10);

        $statusCounts = [
            'pending' => Business::query()
                ->where('status', BusinessStatus::Pending->value)
                ->count(),

            'active' => Business::query()
                ->where('status', BusinessStatus::Active->value)
                ->count(),

            'suspended' => Business::query()
                ->where('status', BusinessStatus::Suspended->value)
                ->count(),
        ];

        return view(
            'livewire.admin.business-manager',
            [
                'businesses' => $businesses,
                'statusOptions' => BusinessStatus::cases(),
                'statusCounts' => $statusCounts,
            ]
        );
    }
```

## Administrator users

[Full PHP file](../app/Livewire/Admin/UserManager.php) ? render starts at line 242. [UI bindings](../resources/views/livewire/admin/user-manager.blade.php).

### Component state (excerpt)

```php
class UserManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'role')]
    public string $roleFilter = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public ?int $selectedUserId = null;

    public string $actionType = '';

    public string $reason = '';
```

### Result query and view

```php
    public function render(): View
    {
        $this->adminUser();

        $validRoles = [
            UserRole::Customer->value,
            UserRole::Provider->value,
        ];

        $validStatuses = array_map(
            fn(UserStatus $status): string => $status->value,
            UserStatus::cases()
        );

        $search = trim($this->search);

        $baseQuery = User::query()->whereIn('role', $validRoles);

        $users = (clone $baseQuery)
            ->with([
                'business' => fn($query) => $query
                    ->select([
                        'id',
                        'owner_id',
                        'name',
                        'status',
                    ])
                    ->withCount('services'),
            ])
            ->withCount('bookings')
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
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
            )
            ->when(
                in_array($this->roleFilter, $validRoles, true),
                fn(Builder $query) => $query->where(
                    'role',
                    $this->roleFilter
                )
            )
            ->when(
                in_array(
                    $this->statusFilter,
                    $validStatuses,
                    true
                ),
                fn(Builder $query) => $query->where(
                    'status',
                    $this->statusFilter
                )
            )
            ->latest()
            ->paginate(10);

        $metrics = [
            'total' => (clone $baseQuery)->count(),

            'customers' => (clone $baseQuery)
                ->where('role', UserRole::Customer->value)
                ->count(),

            'providers' => (clone $baseQuery)
                ->where('role', UserRole::Provider->value)
                ->count(),

            'active' => (clone $baseQuery)
                ->where('status', UserStatus::Active->value)
                ->count(),

            'suspended' => (clone $baseQuery)
                ->where('status', UserStatus::Suspended->value)
                ->count(),
        ];

        return view(
            'livewire.admin.user-manager',
            [
                'users' => $users,
                'metrics' => $metrics,
                'roleOptions' => [
                    UserRole::Customer,
                    UserRole::Provider,
                ],
                'statusOptions' => UserStatus::cases(),
            ]
        );
    }
```

## Administrator bookings

[Full PHP file](../app/Livewire/Admin/BookingMonitor.php) ? render starts at line 101. [UI bindings](../resources/views/livewire/admin/booking-monitor.blade.php).

### Component state (excerpt)

```php
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
```

### Result query and view

```php
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
```

## Customer catalogue

[Full PHP file](../app/Livewire/Customer/ServiceCatalog.php) ? render starts at line 53. [UI bindings](../resources/views/livewire/customer/service-catalog.blade.php).

### Component state (excerpt)

```php
class ServiceCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sort = 'name';

    #[Url]
    public bool $onlyAvailable = false;
```

### Result query and view

```php
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
```

## Service details

[Full PHP file](../app/Livewire/Customer/ServiceDetails.php) ? render starts at line 58. [UI bindings](../resources/views/livewire/customer/service-details.blade.php).

### Component state (excerpt)

```php
class ServiceDetails extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public Service $service;

    public string $dateFilter = '';
```

### Result query and view

```php
    public function render(): View
    {
        $service = Service::query()
            ->with([
                'business:id,name,slug,description,phone,email,address,status',
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
```

## Personal bookings

[Full PHP file](../app/Livewire/Customer/BookingList.php) ? render starts at line 175. [UI bindings](../resources/views/livewire/customer/booking-list.blade.php).

### Component state (excerpt)

```php
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
```

### Result query and view

```php
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
```

## Provider services

[Full PHP file](../app/Livewire/Provider/ServiceManager.php) ? render starts at line 295. [UI bindings](../resources/views/livewire/provider/service-manager.blade.php).

### Component state (excerpt)

```php
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
```

### Result query and view

```php
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
```

## Provider availability

[Full PHP file](../app/Livewire/Provider/AvailabilityManager.php) ? render starts at line 407. [UI bindings](../resources/views/livewire/provider/availability-manager.blade.php).

### Component state (excerpt)

```php
class AvailabilityManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Locked]
    public ?int $businessId = null;

    public ?int $slotId = null;

    public string $serviceId = '';

    public string $startsAt = '';

    public string $capacity = '1';

    public bool $isActive = true;

    public bool $showForm = false;

    public string $filterServiceId = '';

    public string $dateFilter = '';
```

### Result query and view

```php
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
```

## Provider bookings

[Full PHP file](../app/Livewire/Provider/BookingManager.php) ? render starts at line 285. [UI bindings](../resources/views/livewire/provider/booking-manager.blade.php).

### Component state (excerpt)

```php
class BookingManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Locked]
    public ?int $businessId = null;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public string $dateFilter = '';

    public ?int $selectedBookingId = null;

    public string $actionType = '';

    public string $reason = '';
```

### Result query and view

```php
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
```

## Shared model queries: Service

[Full model](../app/Models/Service.php). Exact excerpt beginning at line 63; final class closing brace omitted.

```php
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeVisibleToCustomers(Builder $query): Builder
    {
        return $query
            ->active()
            ->whereHas('business', function (Builder $query): void {
                $query
                    ->where('status', BusinessStatus::Active->value)
                    ->whereHas('owner', function (Builder $query): void {
                        $query->where('status', UserStatus::Active->value);
                    });
            });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term): void {
            $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhereHas('business', function (Builder $query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%");
                });
        });
    }
```

## Shared model queries: AvailabilitySlot

[Full model](../app/Models/AvailabilitySlot.php). Exact excerpt beginning at line 50; final class closing brace omitted.

```php
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
```

## API query code

The API has different input names from some UI pages. Read the controller methods alongside the API table in the learning guide. These are full-file links, not duplicated implementations.

- [ServiceController::index and slots](../app/Http/Controllers/Api/ServiceController.php): validation, service search/sort/availability and slot date.
- [BookingController::index](../app/Http/Controllers/Api/BookingController.php): personal status filter.
- [Provider BookingController::index](../app/Http/Controllers/Api/Provider/BookingController.php): ownership, search, status and date.
