<div class="min-h-[calc(100vh-4rem)] bg-slate-50">
    <x-page-header eyebrow="Welcome back, {{ Auth::user()->name }}" title="Find the right service, book it with ease."
        subtitle="Explore trusted local services, compare availability and reserve a convenient appointment from one simple place.">
        <x-slot:actions>
            <a href="{{ route('customer.bookings.index') }}"
                class="inline-flex w-fit items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 font-bold text-slate-800 shadow-lg shadow-slate-950/5 transition hover:-translate-y-0.5 hover:border-indigo-300 hover:text-indigo-700 hover:shadow-xl">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                    </svg>
                </span>
                <span>
                    <span class="block text-sm">My bookings</span>
                    <span class="block text-xs font-medium text-slate-500">View appointment history</span>
                </span>
                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                </svg>
            </a>
        </x-slot:actions>
    </x-page-header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        {{-- Search and filters --}}
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="grid gap-5 lg:grid-cols-[1fr_15rem]">
                <div>
                    <label for="catalog-search" class="block text-sm font-bold text-slate-700">
                        What service are you looking for?
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

                        <input id="catalog-search" type="search" wire:model.live.debounce.400ms="search"
                            placeholder="Search by service or business name"
                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="catalog-sort" class="block text-sm font-bold text-slate-700">
                        Sort results
                    </label>

                    <select id="catalog-sort" wire:model.live="sort"
                        class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 text-slate-700 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                        <option value="name">Name</option>
                        <option value="price_low">Price: low to high</option>
                        <option value="price_high">Price: high to low</option>
                        <option value="rating">Highest rated</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
            </div>

            <div
                class="mt-5 flex flex-col justify-between gap-4 border-t border-slate-100 pt-5 sm:flex-row sm:items-center">
                <label class="flex cursor-pointer items-center gap-3">
                    <input type="checkbox" wire:model.live="onlyAvailable"
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">
                        Only show services with available appointments
                    </span>
                </label>

                <button type="button" wire:click="resetFilters"
                    class="inline-flex items-center gap-2 text-left text-sm font-bold text-indigo-600 transition hover:text-indigo-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v6h6M20 20v-6h-6M5.5 15a7 7 0 0 0 11.8 2M18.5 9A7 7 0 0 0 6.7 7" />
                    </svg>
                    Clear filters
                </button>
            </div>
        </section>

        <div class="mt-8 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">Explore services</p>
                <h2 class="mt-2 text-2xl font-extrabold text-slate-950">Available for booking</h2>
            </div>

            <p class="text-sm font-medium text-slate-500">
                {{ $services->total() }} {{ \Illuminate\Support\Str::plural('service', $services->total()) }} found
            </p>
        </div>

        <div wire:loading wire:target="search,sort,onlyAvailable,resetFilters"
            class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">
            Updating services...
        </div>

        {{-- Service cards --}}
        <div class="mt-5 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($services as $service)
                <article wire:key="catalog-service-{{ $service->id }}"
                    class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-950/10">
                    <div
                        class="relative flex h-40 items-center justify-center overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-500 to-cyan-500">
                        <div aria-hidden="true"
                            class="absolute -right-8 -top-12 h-36 w-36 rounded-full border-[24px] border-white/10"></div>
                        <div aria-hidden="true" class="absolute -bottom-16 -left-10 h-40 w-40 rounded-full bg-white/10">
                        </div>
                        <span
                            class="relative flex h-20 w-20 items-center justify-center rounded-3xl bg-white text-4xl font-extrabold text-indigo-600 shadow-xl">
                            {{ strtoupper(substr($service->name, 0, 1)) }}
                        </span>

                        @if ($service->available_slots_count > 0)
                            <span
                                class="absolute right-4 top-4 rounded-full bg-white px-3 py-1 text-xs font-bold text-emerald-700 shadow-sm">
                                {{ $service->available_slots_count }} available
                            </span>
                        @else
                            <span
                                class="absolute right-4 top-4 rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white shadow-sm">
                                No slots
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">
                            {{ $service->business->name }}
                        </p>

                        <h3 class="mt-2 text-xl font-extrabold text-slate-950">
                            {{ $service->name }}
                        </h3>

                        <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">
                            {{ \Illuminate\Support\Str::limit($service->description ?? 'No description has been provided.', 120) }}
                        </p>

                        <div
                            class="mt-5 grid grid-cols-3 divide-x divide-slate-200 rounded-2xl bg-slate-50 px-2 py-4 text-center">
                            <div class="px-2">
                                <p class="text-xs font-medium text-slate-500">Duration</p>
                                <p class="mt-1 text-sm font-bold text-slate-900">{{ $service->duration_minutes }} min</p>
                            </div>
                            <div class="px-2">
                                <p class="text-xs font-medium text-slate-500">Price</p>
                                <p class="mt-1 text-sm font-bold text-slate-900">Rs.
                                    {{ number_format((float) $service->price) }}
                                </p>
                            </div>
                            <div class="px-2">
                                <p class="text-xs font-medium text-slate-500">Rating</p>
                                <p class="mt-1 text-sm font-bold text-slate-900">
                                    {{ $service->reviews_avg_rating !== null ? number_format((float) $service->reviews_avg_rating, 1) : 'New' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 flex items-start gap-2 text-sm text-slate-500">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                                <circle cx="12" cy="10" r="2"></circle>
                            </svg>
                            <span>{{ $service->business->address ?? 'Location not provided' }}</span>
                        </div>

                        <a href="{{ route('customer.services.show', $service) }}"
                            class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/15 transition hover:from-indigo-700 hover:to-cyan-600">
                            View service
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        </a>
                    </div>
                </article>
            @empty
                <div
                    class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm md:col-span-2 xl:col-span-3">
                    <span
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path stroke-linecap="round" d="m20 20-3.5-3.5"></path>
                        </svg>
                    </span>
                    <h2 class="mt-5 text-xl font-extrabold text-slate-950">No services found</h2>
                    <p class="mt-2 text-slate-600">Try changing your search or availability filter.</p>
                    <button type="button" wire:click="resetFilters"
                        class="mt-6 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                        Clear filters
                    </button>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $services->links() }}
        </div>
    </main>
</div>