<div class="min-h-screen bg-slate-50 pb-16">
    <x-page-header eyebrow="Admin workspace" title="Manage businesses"
        subtitle="Review provider profiles, approve new businesses, and control their visibility across BookEase.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:self-auto">
                <span aria-hidden="true">←</span> Dashboard
            </a>
        </x-slot:actions>
    </x-page-header>

    <main class="relative z-10 mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        @if (session('success'))
            <div role="alert"
                class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>{{ session('success') }}
            </div>
        @endif

        @error('action')
            <div role="alert"
                class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 shadow-sm">
                {{ $message }}</div>
        @enderror

        <section class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-3xl border border-amber-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-600">Pending approval</p>
                        <p class="mt-2 text-3xl font-black text-amber-700">{{ number_format($statusCounts['pending']) }}
                        </p>
                    </div><span
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"><svg
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg></span>
                </div>
                <p class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-500">Waiting for administrator review
                </p>
            </article>
            <article class="rounded-3xl border border-emerald-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-600">Active businesses</p>
                        <p class="mt-2 text-3xl font-black text-emerald-700">
                            {{ number_format($statusCounts['active']) }}</p>
                    </div><span
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><svg
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg></span>
                </div>
                <p class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-500">Visible and bookable by customers
                </p>
            </article>
            <article class="rounded-3xl border border-rose-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-600">Suspended</p>
                        <p class="mt-2 text-3xl font-black text-rose-700">
                            {{ number_format($statusCounts['suspended']) }}</p>
                    </div><span
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-600"><svg
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18.364 5.636 5.636 18.364m12.728 0L5.636 5.636" />
                        </svg></span>
                </div>
                <p class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-500">Hidden from the customer catalogue
                </p>
            </article>
        </section>

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Business directory</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $businesses->total() }}
                        {{ \Illuminate\Support\Str::plural('business', $businesses->total()) }} found</h2>
                </div>
                <div class="grid flex-1 gap-3 sm:grid-cols-[minmax(0,1fr)_12rem] lg:max-w-3xl">
                    <div>
                        <label for="business-search"
                            class="text-xs font-bold uppercase tracking-wide text-slate-500">Search</label>
                        <div class="relative mt-1"><svg
                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-4.35-4.35m1.6-5.4a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                            </svg><input id="business-search" type="search" wire:model.live.debounce.400ms="search"
                                placeholder="Business, owner, email or phone"
                                class="block w-full rounded-xl border-slate-300 py-2.5 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label for="business-status-filter"
                            class="text-xs font-bold uppercase tracking-wide text-slate-500">Status</label>
                        <select id="business-status-filter" wire:model.live="statusFilter"
                            class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All statuses</option>
                            @foreach ($statusOptions as $status)
                            <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>@endforeach
                        </select>
                    </div>
                </div>
            </div>
            @if ($search !== '' || $statusFilter !== '')
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                    <p class="text-xs text-slate-500">Filters are currently applied</p><button type="button"
                        wire:click="resetFilters" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Clear all
                        filters</button>
                </div>
            @endif
        </section>

        <div wire:loading wire:target="search,statusFilter,resetFilters"
            class="mt-4 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">Updating businesses...
        </div>

        <div class="mt-5 space-y-5">
            @forelse ($businesses as $business)
                @php
                    $statusClasses = match ($business->status->value) {
                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
                        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                        'suspended' => 'bg-rose-50 text-rose-700 ring-rose-200',
                        default => 'bg-slate-100 text-slate-600 ring-slate-200',
                    };
                    $initial = str($business->name)->substr(0, 1)->upper();
                @endphp

                <article wire:key="admin-business-{{ $business->id }}"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                    <header
                        class="flex flex-col gap-5 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div class="flex min-w-0 items-center gap-4">
                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-700 text-xl font-black text-white shadow-md shadow-indigo-200">
                                {{ $initial }}</div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate text-xl font-bold text-slate-900">{{ $business->name }}</h3><span
                                        class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $statusClasses }}">{{ ucfirst($business->status->value) }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">Registered
                                    {{ $business->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            @if ($business->status === \App\Enums\BusinessStatus::Pending)
                                <button type="button" wire:click="approveBusiness({{ $business->id }})"
                                    wire:confirm="Approve this business?" wire:loading.attr="disabled"
                                    class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-100 hover:bg-emerald-700 disabled:opacity-50">Approve</button>
                                <button type="button" wire:click="openSuspendForm({{ $business->id }})"
                                    class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-50">Suspend</button>
                            @elseif ($business->status === \App\Enums\BusinessStatus::Active)
                                <button type="button" wire:click="openSuspendForm({{ $business->id }})"
                                    class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-50">Suspend
                                    business</button>
                            @elseif ($business->status === \App\Enums\BusinessStatus::Suspended)
                                <button type="button" wire:click="reactivateBusiness({{ $business->id }})"
                                    wire:confirm="Reactivate this business?" wire:loading.attr="disabled"
                                    class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-100 hover:bg-emerald-700 disabled:opacity-50">Reactivate</button>
                            @endif
                        </div>
                    </header>

                    <div class="p-5 sm:p-6">
                        <div class="grid gap-4 md:grid-cols-3">
                            <section class="rounded-2xl bg-slate-50 p-4">
                                <div class="flex items-center gap-2 text-slate-400"><svg class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 7.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM4.5 21a7.5 7.5 0 0 1 15 0" />
                                    </svg>
                                    <h4 class="text-xs font-bold uppercase tracking-wide">Owner</h4>
                                </div>
                                <p class="mt-3 font-bold text-slate-900">{{ $business->owner->name }}</p>
                                <p class="mt-0.5 break-all text-sm text-slate-500">{{ $business->owner->email }}</p>
                                <p class="mt-2 text-xs text-slate-500">Account: <span
                                        class="font-bold text-slate-700">{{ ucfirst($business->owner->status->value) }}</span>
                                </p>
                            </section>
                            <section class="rounded-2xl bg-slate-50 p-4">
                                <div class="flex items-center gap-2 text-slate-400"><svg class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0-8.661 5.197a2.25 2.25 0 0 1-2.178 0L2.25 6.75" />
                                    </svg>
                                    <h4 class="text-xs font-bold uppercase tracking-wide">Contact</h4>
                                </div>
                                <p class="mt-3 break-all text-sm font-semibold text-slate-800">
                                    {{ $business->email ?? 'No business email' }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $business->phone ?? 'No phone number' }}</p>
                                <p class="mt-1 text-sm leading-5 text-slate-600">
                                    {{ $business->address ?? 'No address provided' }}</p>
                            </section>
                            <section class="rounded-2xl bg-slate-50 p-4">
                                <div class="flex items-center gap-2 text-slate-400"><svg class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m9 12.75 2.25 2.25L15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <h4 class="text-xs font-bold uppercase tracking-wide">Services</h4>
                                </div>
                                <p class="mt-3 text-2xl font-black text-slate-900">{{ $business->services_count }}</p>
                                <p class="mt-1 text-sm text-slate-500">Total services</p>
                                <p class="mt-2 text-xs font-bold text-emerald-600">{{ $business->active_services_count }}
                                    currently active</p>
                            </section>
                        </div>

                        @if ($business->description)
                            <div class="mt-4 rounded-2xl border border-slate-100 bg-white p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">About the business</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $business->description }}</p>
                            </div>
                        @endif

                        @if ($selectedBusinessId === $business->id && $actionType === 'suspend')
                            <form wire:submit="suspendBusiness" class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-5">
                                <div class="flex items-start gap-3"><span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700"><svg
                                            class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                        </svg></span>
                                    <div>
                                        <h4 class="font-bold text-rose-900">Suspend {{ $business->name }}?</h4>
                                        <p class="mt-1 text-sm text-rose-700">Its services will no longer be visible or bookable
                                            by customers.</p>
                                    </div>
                                </div>
                                <label for="reason-{{ $business->id }}"
                                    class="mt-4 block text-sm font-bold text-rose-900">Suspension reason</label>
                                <textarea id="reason-{{ $business->id }}" rows="4" maxlength="1000" wire:model.blur="reason"
                                    placeholder="Explain why this business is being suspended..."
                                    class="mt-2 block w-full rounded-xl border-rose-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                                @error('reason')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p> @enderror
                                <div class="mt-4 flex justify-end gap-3"><button type="button" wire:click="closeActionForm"
                                        class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Close</button><button
                                        type="submit" wire:loading.attr="disabled" wire:target="suspendBusiness"
                                        class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700 disabled:opacity-50"><span
                                            wire:loading.remove wire:target="suspendBusiness">Confirm suspension</span><span
                                            wire:loading wire:target="suspendBusiness">Suspending...</span></button></div>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 21v-4.5h6V21" />
                        </svg></div>
                    <h2 class="mt-4 text-lg font-bold text-slate-900">No businesses found</h2>
                    <p class="mt-2 text-sm text-slate-500">No businesses match the current filters.</p>
                    @if ($search !== '' || $statusFilter !== '')<button type="button" wire:click="resetFilters"
                        class="mt-5 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Clear
                    filters</button>@endif
                </div>
            @endforelse
        </div>

        @if ($businesses->hasPages())
        <div class="mt-8">{{ $businesses->links() }}</div>@endif
    </main>
</div>