<div class="min-h-[calc(100vh-4rem)] bg-slate-50">
    <x-page-header :back="['href' => route('customer.services.index'), 'label' => 'Back to services']"
        eyebrow="{{ $service->business->name }}" title="{{ $service->name }}"
        subtitle="Choose an available appointment below and reserve your preferred time securely through BookEase.">
        <x-slot:actions>
            <div class="grid min-w-72 grid-cols-2 gap-px overflow-hidden rounded-2xl border border-slate-200 bg-slate-200 shadow-sm">
                <div class="bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        Price
                    </p>

                    <p class="mt-2 text-2xl font-extrabold text-slate-950">
                        Rs. {{ number_format(
    (float) $service->price,
    2
) }}
                    </p>
                </div>

                <div class="bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        Duration
                    </p>

                    <p class="mt-2 text-2xl font-extrabold text-slate-950">
                        {{ $service->duration_minutes }}
                        <span class="text-sm font-semibold text-slate-500">
                            min
                        </span>
                    </p>
                </div>

                <div class="col-span-2 bg-white p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Customer rating
                            </p>

                            <p class="mt-2 font-bold text-slate-950">
                                @if ($service->reviews_avg_rating !== null)
                                                                    {{ number_format(
                                        (float) $service->reviews_avg_rating,
                                        1
                                    ) }}
                                                                    out of 5
                                @else
                                    New service
                                @endif
                            </p>
                        </div>

                        @if ($service->reviews_avg_rating !== null)
                            <span
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-xl text-amber-500">
                                ★
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        {{-- Service and business information --}}
        <div class="grid gap-6 lg:grid-cols-[1fr_23rem]">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">
                    Service information
                </p>

                <h2 class="mt-3 text-2xl font-extrabold text-slate-950">
                    About this service
                </h2>

                <p class="mt-5 whitespace-pre-line text-base leading-8 text-slate-600">
                    {{ $service->description
    ?? 'No service description has been provided.' }}
                </p>
            </section>

            <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-cyan-500 text-xl font-extrabold text-white shadow-lg shadow-indigo-500/15">
                        {{ strtoupper(substr(
    $service->business->name,
    0,
    1
)) }}
                    </span>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Service provider
                        </p>

                        <h2 class="mt-1 text-lg font-extrabold text-slate-950">
                            {{ $service->business->name }}
                        </h2>
                    </div>
                </div>

                @if ($service->business->description)
                                <p class="mt-5 text-sm leading-6 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit(
                        $service->business->description,
                        180
                    ) }}
                                </p>
                @endif

                <dl class="mt-6 space-y-4 border-t border-slate-100 pt-5">
                    <div class="flex items-start gap-3">
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />

                                <circle cx="12" cy="10" r="2"></circle>
                            </svg>
                        </span>

                        <div>
                            <dt class="text-xs font-bold text-slate-400">
                                Address
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-700">
                                {{ $service->business->address
    ?? 'Not provided' }}
                            </dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.6 3h3l1.5 4-2 1.5a15 15 0 0 0 6.4 6.4l1.5-2 4 1.5v3a3 3 0 0 1-3 3C9.7 20.4 3.6 14.3 3.6 6a3 3 0 0 1 3-3Z" />
                            </svg>
                        </span>

                        <div>
                            <dt class="text-xs font-bold text-slate-400">
                                Phone
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-700">
                                {{ $service->business->phone
    ?? 'Not provided' }}
                            </dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z" />

                                <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6" />
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <dt class="text-xs font-bold text-slate-400">
                                Email
                            </dt>

                            <dd class="mt-1 break-all text-sm font-semibold text-slate-700">
                                {{ $service->business->email
    ?? 'Not provided' }}
                            </dd>
                        </div>
                    </div>
                </dl>
            </aside>
        </div>

        {{-- Appointment slots --}}
        <section id="appointments" class="mt-12">
            <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">
                        Book an appointment
                    </p>

                    <h2 class="mt-2 text-3xl font-extrabold text-slate-950">
                        Available appointment times
                    </h2>

                    <p class="mt-2 text-slate-600">
                        Select a date or choose from the available times below.
                    </p>
                </div>

                <div class="flex items-end gap-3">
                    <div>
                        <label for="dateFilter" class="block text-sm font-bold text-slate-700">
                            Filter by date
                        </label>

                        <input id="dateFilter" type="date" min="{{ now()->format('Y-m-d') }}"
                            wire:model.live="dateFilter"
                            class="mt-2 rounded-2xl border-slate-200 bg-white py-3 text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @if ($dateFilter !== '')
                        <button type="button" wire:click="clearDateFilter"
                            class="mb-3 text-sm font-bold text-indigo-600 transition hover:text-indigo-800">
                            Clear
                        </button>
                    @endif
                </div>
            </div>

            @error('dateFilter')
                <p class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                    {{ $message }}
                </p>
            @enderror

            <div wire:loading wire:target="dateFilter,clearDateFilter"
                class="mt-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">
                Updating appointment times...
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @forelse ($slots as $slot)
                                @php
                                    $remainingCapacity = max(
                                        0,
                                        $slot->capacity -
                                        $slot->active_bookings_count
                                    );
                                @endphp

                                <article wire:key="customer-slot-{{ $slot->id }}"
                                    class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-950/10">
                                    <div class="flex items-start gap-4 p-6">
                                        <div
                                            class="w-20 shrink-0 overflow-hidden rounded-2xl border border-indigo-100 bg-indigo-50 text-center">
                                            <p class="bg-indigo-600 px-2 py-1.5 text-xs font-bold uppercase tracking-wide text-white">
                                                {{ $slot->starts_at->format('M') }}
                                            </p>

                                            <p class="py-2 text-2xl font-extrabold text-indigo-700">
                                                {{ $slot->starts_at->format('d') }}
                                            </p>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-start justify-between gap-3">
                                                <div>
                                                    <p class="font-extrabold text-slate-950">
                                                        {{ $slot->starts_at->format(
                        'l, d F Y'
                    ) }}
                                                    </p>

                                                    <p class="mt-2 text-lg font-extrabold text-indigo-700">
                                                        {{ $slot->starts_at->format(
                        'h:i A'
                    ) }}
                                                        –
                                                        {{ $slot->ends_at->format(
                        'h:i A'
                    ) }}
                                                    </p>
                                                </div>

                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                                    Available
                                                </span>
                                            </div>

                                            <div class="mt-4 flex items-center gap-2 text-sm text-slate-500">
                                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8" />

                                                    <path stroke-linecap="round" d="M19 8v6M22 11h-6" />
                                                </svg>

                                                {{ $remainingCapacity }}
                                                {{ \Illuminate\Support\Str::plural(
                        'place',
                        $remainingCapacity
                    ) }}
                                                remaining
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-slate-100 bg-slate-50/70 p-4">
                                        <a href="{{ route(
                        'customer.bookings.create',
                        [
                            'service' => $service,
                            'slot' => $slot,
                        ]
                    ) }}"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/15 transition hover:from-indigo-700 hover:to-cyan-600">
                                            Book this appointment

                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                            </svg>
                                        </a>
                                    </div>
                                </article>
                @empty
                    <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-sm md:col-span-2">
                        <span
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                            </svg>
                        </span>

                        <h3 class="mt-5 text-xl font-extrabold text-slate-950">
                            No available appointments
                        </h3>

                        <p class="mt-2 text-slate-600">
                            Try another date or check again later.
                        </p>

                        @if ($dateFilter !== '')
                            <button type="button" wire:click="clearDateFilter"
                                class="mt-6 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                                Show all dates
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $slots->links() }}
            </div>
        </section>

        {{-- Reviews --}}
        <section class="mt-14">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">
                    Customer feedback
                </p>

                <h2 class="mt-2 text-3xl font-extrabold text-slate-950">
                    Customer reviews
                </h2>

                <p class="mt-2 text-slate-600">
                    See what previous customers said about this service.
                </p>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @forelse ($reviews as $review)
                                <article wire:key="review-{{ $review->id }}"
                                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-cyan-500 text-sm font-extrabold text-white">
                                                {{ strtoupper(substr(
                        $review->customer->name,
                        0,
                        1
                    )) }}
                                            </span>

                                            <div>
                                                <p class="font-extrabold text-slate-900">
                                                    {{ $review->customer->name }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $review->created_at->format(
                        'd M Y'
                    ) }}
                                                </p>
                                            </div>
                                        </div>

                                        <span
                                            class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-sm font-bold text-amber-700">
                                            <span class="text-amber-500">★</span>
                                            {{ $review->rating }}/5
                                        </span>
                                    </div>

                                    <p class="mt-5 leading-7 text-slate-600">
                                        {{ $review->comment
                        ?? 'The customer left a rating without a comment.' }}
                                    </p>
                                </article>
                @empty
                    <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm md:col-span-2">
                        <span class="text-3xl text-amber-400">☆</span>

                        <h3 class="mt-3 text-lg font-extrabold text-slate-950">
                            No customer reviews yet
                        </h3>

                        <p class="mt-2 text-slate-600">
                            This service has not received customer feedback.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</div>