<div class="min-h-[calc(100vh-4rem)] bg-slate-50">
    <x-page-header title="My bookings"
        subtitle="View upcoming appointments, track booking statuses, manage cancellations and review completed services.">
        @if (Auth::user()->isCustomer())
            <x-slot:actions>
                <a href="{{ route('customer.services.index') }}"
                    class="inline-flex w-fit items-center gap-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-6 py-4 font-bold text-white shadow-xl shadow-indigo-500/20 transition hover:-translate-y-0.5 hover:from-indigo-700 hover:to-cyan-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path stroke-linecap="round" d="m20 20-3.5-3.5"></path>
                    </svg>

                    Browse services
                </a>
            </x-slot:actions>
        @endif
    </x-page-header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        @if (session('success'))
            <div role="alert"
                class="mb-7 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                </svg>

                {{ session('success') }}
            </div>
        @endif

        {{-- Search and filters --}}
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="grid gap-5 lg:grid-cols-[1fr_15rem]">
                <div>
                    <label for="booking-search" class="block text-sm font-bold text-slate-700">
                        Search your bookings
                    </label>

                    <div class="relative mt-2">
                        <span
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path stroke-linecap="round" d="m20 20-3.5-3.5"></path>
                            </svg>
                        </span>

                        <input id="booking-search" type="search" wire:model.live.debounce.400ms="search"
                            placeholder="Reference, service or business name"
                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="status-filter" class="block text-sm font-bold text-slate-700">
                        Booking status
                    </label>

                    <select id="status-filter" wire:model.live="statusFilter"
                        class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 text-slate-700 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                        <option value="">All statuses</option>

                        @foreach ($statusOptions as $status)
                            <option value="{{ $status->value }}">
                                {{ ucfirst($status->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($search !== '' || $statusFilter !== '')
                <div class="mt-5 border-t border-slate-100 pt-5">
                    <button type="button" wire:click="resetFilters"
                        class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 transition hover:text-indigo-800">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v6h6M20 20v-6h-6M5.5 15a7 7 0 0 0 11.8 2M18.5 9A7 7 0 0 0 6.7 7" />
                        </svg>

                        Clear filters
                    </button>
                </div>
            @endif
        </section>

        <div wire:loading wire:target="search,statusFilter,resetFilters"
            class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">
            Updating bookings...
        </div>

        <div class="mt-8 flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">
                    Appointment records
                </p>

                <h2 class="mt-2 text-2xl font-extrabold text-slate-950">
                    Your bookings
                </h2>
            </div>

            <p class="text-sm font-medium text-slate-500">
                {{ $bookings->total() }}
                {{ \Illuminate\Support\Str::plural(
    'booking',
    $bookings->total()
) }}
                found
            </p>
        </div>

        {{-- Booking cards --}}
        <div class="mt-5 space-y-6">
            @forelse ($bookings as $booking)
                        @php
                            $statusClasses = match ($booking->status->value) {
                                'pending' =>
                                'border-amber-200 bg-amber-50 text-amber-700',
                                'confirmed' =>
                                'border-blue-200 bg-blue-50 text-blue-700',
                                'completed' =>
                                'border-emerald-200 bg-emerald-50 text-emerald-700',
                                'cancelled' =>
                                'border-slate-200 bg-slate-100 text-slate-600',
                                'rejected' =>
                                'border-rose-200 bg-rose-50 text-rose-700',
                                default =>
                                'border-slate-200 bg-slate-100 text-slate-600',
                            };

                            $statusDot = match ($booking->status->value) {
                                'pending' => 'bg-amber-500',
                                'confirmed' => 'bg-blue-500',
                                'completed' => 'bg-emerald-500',
                                'cancelled' => 'bg-slate-400',
                                'rejected' => 'bg-rose-500',
                                default => 'bg-slate-400',
                            };

                            $appointmentIsPast =
                                $booking->slot->ends_at->isPast();
                        @endphp

                        <article wire:key="customer-booking-{{ $booking->id }}"
                            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-lg hover:shadow-slate-950/5">
                            {{-- Card heading --}}
                            <div
                                class="flex flex-col justify-between gap-5 border-b border-slate-100 px-6 py-6 sm:flex-row sm:items-start">
                                <div class="flex items-start gap-4">
                                    <span
                                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-cyan-500 text-xl font-extrabold text-white shadow-lg shadow-indigo-500/15">
                                        {{ strtoupper(substr(
                    $booking->service->name,
                    0,
                    1
                )) }}
                                    </span>

                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-xl font-extrabold text-slate-950">
                                                {{ $booking->service->name }}
                                            </h3>

                                            <span
                                                class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-bold {{ $statusClasses }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $statusDot }}"></span>

                                                {{ ucfirst($booking->status->value) }}
                                            </span>

                                            @if ($appointmentIsPast)
                                                <span
                                                    class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-bold text-slate-500">
                                                    Past appointment
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-sm font-bold text-indigo-600">
                                            {{ $booking->service->business->name }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Reference:
                                            <span class="font-semibold text-slate-700">
                                                {{ $booking->booking_reference }}
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="sm:text-right">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Total price
                                    </p>

                                    <p class="mt-1 text-2xl font-extrabold text-slate-950">
                                        Rs. {{ number_format(
                    (float) $booking->price,
                    2
                ) }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-6">
                                {{-- Appointment details --}}
                                <dl
                                    class="grid overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 sm:grid-cols-2 lg:grid-cols-4">
                                    <div class="p-4">
                                        <dt
                                            class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                                            <svg class="h-4 w-4 text-indigo-500" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                                            </svg>

                                            Date
                                        </dt>

                                        <dd class="mt-2 font-bold text-slate-900">
                                            {{ $booking->slot->starts_at->format(
                    'D, d M Y'
                ) }}
                                        </dd>
                                    </div>

                                    <div class="border-t border-slate-200 p-4 sm:border-l sm:border-t-0">
                                        <dt
                                            class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                                            <svg class="h-4 w-4 text-indigo-500" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path stroke-linecap="round" d="M12 7v5l3 2" />
                                            </svg>

                                            Time
                                        </dt>

                                        <dd class="mt-2 font-bold text-slate-900">
                                            {{ $booking->slot->starts_at->format(
                    'h:i A'
                ) }}
                                            –
                                            {{ $booking->slot->ends_at->format(
                    'h:i A'
                ) }}
                                        </dd>
                                    </div>

                                    <div class="border-t border-slate-200 p-4 lg:border-l lg:border-t-0">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Duration
                                        </dt>

                                        <dd class="mt-2 font-bold text-slate-900">
                                            {{ $booking->service->duration_minutes }}
                                            minutes
                                        </dd>
                                    </div>

                                    <div class="border-t border-slate-200 p-4 sm:border-l lg:border-t-0">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Location
                                        </dt>

                                        <dd class="mt-2 font-bold text-slate-900">
                                            {{ $booking->service->business->address
                    ?? 'Contact the provider' }}
                                        </dd>
                                    </div>
                                </dl>

                                @if ($booking->notes)
                                    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-sm font-bold text-slate-800">
                                            Your notes
                                        </p>

                                        <p class="mt-1 text-sm leading-6 text-slate-600">
                                            {{ $booking->notes }}
                                        </p>
                                    </div>
                                @endif

                                @if ($booking->cancellation_reason)
                                    <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4">
                                        <p class="text-sm font-bold text-rose-800">
                                            Cancellation reason
                                        </p>

                                        <p class="mt-1 text-sm leading-6 text-rose-700">
                                            {{ $booking->cancellation_reason }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div
                                    class="mt-6 flex flex-col justify-between gap-4 border-t border-slate-100 pt-5 sm:flex-row sm:items-center">
                                    @if (Auth::user()->isCustomer())
                                    <a href="{{ route(
                    'customer.services.show',
                    $booking->service
                ) }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 transition hover:text-indigo-800">
                                        View service

                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                        </svg>
                                    </a>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-3">
                                        @if (
                                                $booking->status ===
                                                \App\Enums\BookingStatus::Completed
                                            )
                                            @if ($booking->review === null)
                                                                <a href="{{ route(
                                                    'customer.bookings.review',
                                                    $booking
                                                ) }}"
                                                                    class="rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:from-indigo-700 hover:to-cyan-600">
                                                                    Write a review
                                                                </a>
                                            @else
                                                <span
                                                    class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-sm font-bold text-emerald-700">
                                                    Reviewed:
                                                    {{ $booking->review->rating }}/5
                                                </span>
                                            @endif
                                        @endif

                                        @if ($booking->canBeCancelledByCustomer())
                                            <button type="button" wire:click="startCancellation({{ $booking->id }})"
                                                class="rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-sm font-bold text-rose-700 transition hover:bg-rose-50">
                                                Cancel booking
                                            </button>
                                        @elseif (
                                                in_array(
                                                    $booking->status->value,
                                                    ['pending', 'confirmed'],
                                                    true
                                                )
                                                && !$appointmentIsPast
                                            )
                                            <p class="max-w-xs text-xs leading-5 text-slate-500">
                                                Customer cancellation closes 24 hours
                                                before the appointment.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Cancellation form --}}
                                @if ($cancellingBookingId === $booking->id)
                                    <form wire:submit="confirmCancellation"
                                        class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-5">
                                        <div class="flex items-start gap-3">
                                            <span
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4M12 17h.01" />

                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10.3 4.4 2.7 18a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 4.4a2 2 0 0 0-3.4 0Z" />
                                                </svg>
                                            </span>

                                            <div>
                                                <h4 class="font-extrabold text-rose-900">
                                                    Cancel this booking?
                                                </h4>

                                                <p class="mt-1 text-sm text-rose-700">
                                                    This action cannot be undone.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <label for="cancellationReason-{{ $booking->id }}"
                                                class="block text-sm font-bold text-rose-900">
                                                Reason for cancellation
                                            </label>

                                            <textarea id="cancellationReason-{{ $booking->id }}" rows="4" maxlength="1000"
                                                wire:model.blur="cancellationReason" placeholder="Please explain why you are cancelling"
                                                class="mt-2 block w-full rounded-2xl border-rose-200 bg-white shadow-sm focus:border-rose-500 focus:ring-rose-500"></textarea>

                                            @error('cancellationReason')
                                                <p class="mt-2 text-sm font-medium text-rose-700">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <div class="mt-5 flex flex-wrap justify-end gap-3">
                                            <button type="button" wire:click="closeCancellation"
                                                class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                                Keep booking
                                            </button>

                                            <button type="submit" wire:loading.attr="disabled" wire:target="confirmCancellation"
                                                class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700 disabled:opacity-50">
                                                <span wire:loading.remove wire:target="confirmCancellation">
                                                    Confirm cancellation
                                                </span>

                                                <span wire:loading wire:target="confirmCancellation">
                                                    Cancelling...
                                                </span>
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </article>
            @empty
                <div class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                    <span
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                        </svg>
                    </span>

                    <h2 class="mt-5 text-xl font-extrabold text-slate-950">
                        No bookings found
                    </h2>

                    <p class="mt-2 text-slate-600">
                        You do not have any matching booking records.
                    </p>

                    @if ($search !== '' || $statusFilter !== '')
                        <button type="button" wire:click="resetFilters"
                            class="mt-6 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            Clear filters
                        </button>
                    @elseif (Auth::user()->isCustomer())
                        <a href="{{ route('customer.services.index') }}"
                            class="mt-6 inline-flex rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/20">
                            Find a service
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $bookings->links() }}
        </div>
    </main>
</div>
