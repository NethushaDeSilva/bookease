<div class="min-h-screen bg-slate-50 pb-16">
    <x-page-header title="Activity logs"
        subtitle="Inspect the platform audit trail and trace important user, business, service, and booking events.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:self-auto"><span
                    aria-hidden="true">←</span> Dashboard</a>
        </x-slot:actions>
    </x-page-header>

    <main class="relative z-10 mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $metricCards = [
                    ['Total events', $metrics['total'], 'bg-indigo-50 text-indigo-600', 'Complete recorded audit history'],
                    ['Events today', $metrics['today'], 'bg-blue-50 text-blue-600', 'Actions recorded today'],
                    ['Last 24 hours', $metrics['last_24_hours'], 'bg-violet-50 text-violet-600', 'Recent platform activity'],
                    ['Users involved', $metrics['users_involved'], 'bg-emerald-50 text-emerald-600', 'Distinct identified users'],
                ];
            @endphp
            @foreach ($metricCards as [$label, $count, $classes, $caption])
                <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">{{ $label }}</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($count) }}</p>
                        </div><span class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $classes }}"><svg
                                class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 6h9.75M10.5 12h9.75m-9.75 6h9.75M3.75 6H4.5v.75h-.75V6Zm0 6h.75v.75h-.75V12Zm0 6h.75v.75h-.75V18Z" />
                            </svg></span>
                    </div>
                    <p class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-500">{{ $caption }}</p>
                </article>
            @endforeach
        </section>

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Event distribution</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Events by category</h2>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                @foreach ($categoryCounts as $category => $count)
                    @php
                        [$categoryCard, $categoryDot] = match ($category) {
                            'booking' => ['bg-blue-50 text-blue-700', 'bg-blue-500'], 'business' => ['bg-violet-50 text-violet-700', 'bg-violet-500'], 'user' => ['bg-rose-50 text-rose-700', 'bg-rose-500'], 'review' => ['bg-amber-50 text-amber-700', 'bg-amber-500'], 'availability' => ['bg-emerald-50 text-emerald-700', 'bg-emerald-500'], 'service' => ['bg-indigo-50 text-indigo-700', 'bg-indigo-500'], default => ['bg-slate-50 text-slate-700', 'bg-slate-500'],
                        };
                    @endphp
                    <article class="rounded-2xl p-4 {{ $categoryCard }}">
                        <div class="flex items-center gap-2"><span
                                class="h-2.5 w-2.5 rounded-full {{ $categoryDot }}"></span>
                            <p class="text-sm font-bold capitalize">{{ $category }}</p>
                        </div>
                        <p class="mt-3 text-2xl font-black">{{ number_format($count) }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Audit records</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $logs->total() }}
                        {{ \Illuminate\Support\Str::plural('event', $logs->total()) }} found</h2>
                </div>
                <div class="grid flex-1 gap-3 sm:grid-cols-2 lg:max-w-4xl lg:grid-cols-[minmax(0,1fr)_12rem_11rem]">
                    <div class="sm:col-span-2 lg:col-span-1"><label for="activity-search"
                            class="text-xs font-bold uppercase tracking-wide text-slate-500">Search</label>
                        <div class="relative mt-1"><svg
                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-4.35-4.35m1.6-5.4a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                            </svg><input id="activity-search" type="search" wire:model.live.debounce.400ms="search"
                                placeholder="Action, user, entity or IP address"
                                class="block w-full rounded-xl border-slate-300 py-2.5 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div><label for="activity-category"
                            class="text-xs font-bold uppercase tracking-wide text-slate-500">Category</label><select
                            id="activity-category" wire:model.live="categoryFilter"
                            class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All categories</option>@foreach ($categoryOptions as $category)
                            <option value="{{ $category }}">{{ ucfirst($category) }}</option>@endforeach
                        </select></div>
                    <div><label for="activity-date"
                            class="text-xs font-bold uppercase tracking-wide text-slate-500">Date</label><input
                            id="activity-date" type="date" wire:model.live="dateFilter"
                            class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">@error('dateFilter')
                            <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            @if ($search !== '' || $categoryFilter !== '' || $dateFilter !== '')
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                    <p class="text-xs text-slate-500">Filters are currently applied</p><button type="button"
                        wire:click="resetFilters" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Clear all
                        filters</button>
            </div>@endif
        </section>

        <div wire:loading wire:target="search,categoryFilter,dateFilter,resetFilters,toggleDetails"
            class="mt-4 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">Updating activity
            logs...</div>

        <div
            class="relative mt-6 space-y-4 before:absolute before:bottom-5 before:left-[1.45rem] before:top-5 before:w-px before:bg-slate-200 sm:before:left-[2.45rem]">
            @forelse ($logs as $log)
                @php
                    $category = str($log->action)->before('.')->toString();
                    [$categoryClasses, $markerClasses] = match ($category) {
                        'booking' => ['bg-blue-50 text-blue-700 ring-blue-200', 'bg-blue-500 ring-blue-100'], 'business' => ['bg-violet-50 text-violet-700 ring-violet-200', 'bg-violet-500 ring-violet-100'], 'user' => ['bg-rose-50 text-rose-700 ring-rose-200', 'bg-rose-500 ring-rose-100'], 'review' => ['bg-amber-50 text-amber-700 ring-amber-200', 'bg-amber-500 ring-amber-100'], 'availability' => ['bg-emerald-50 text-emerald-700 ring-emerald-200', 'bg-emerald-500 ring-emerald-100'], 'service' => ['bg-indigo-50 text-indigo-700 ring-indigo-200', 'bg-indigo-500 ring-indigo-100'], default => ['bg-slate-100 text-slate-600 ring-slate-200', 'bg-slate-500 ring-slate-100'],
                    };
                @endphp
                <div class="relative pl-12 sm:pl-20">
                    <span class="absolute left-3 top-7 h-5 w-5 rounded-full ring-8 sm:left-8 {{ $markerClasses }}"></span>
                    <article wire:key="activity-log-{{ $log->id }}"
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                        <div class="flex flex-col gap-5 p-5 lg:flex-row lg:items-start lg:justify-between sm:p-6">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-slate-900">{{ str($log->action)->replace('.', ' ')->title() }}
                                    </h3><span
                                        class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $categoryClasses }}">{{ ucfirst($category) }}</span>
                                </div>
                                <dl class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Performed by
                                        </dt>
                                        <dd class="mt-2 truncate font-bold text-slate-900">
                                            {{ $log->user?->name ?? 'System or deleted user' }}</dd>@if ($log->user)
                                                <dd class="mt-0.5 truncate text-sm text-slate-500">{{ $log->user->email }}</dd>
                                            @endif
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Entity</dt>
                                        <dd class="mt-2 font-bold text-slate-900">
                                            @if ($log->entity_type){{ class_basename($log->entity_type) }} <span
                                            class="text-indigo-600">#{{ $log->entity_id }}</span>@else Not associated
                                                @endif</dd>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">IP address</dt>
                                        <dd class="mt-2 break-all font-mono text-sm font-bold text-slate-800">
                                            {{ $log->ip_address ?? 'Not recorded' }}</dd>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Timestamp</dt>
                                        <dd class="mt-2 font-bold text-slate-900">{{ $log->created_at->format('d M Y') }}
                                        </dd>
                                        <dd class="mt-0.5 text-sm text-slate-500">{{ $log->created_at->format('h:i:s A') }}
                                            · {{ $log->created_at->diffForHumans() }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <button type="button" wire:click="toggleDetails({{ $log->id }})"
                                class="shrink-0 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">{{ $selectedLogId === $log->id ? 'Hide details' : 'View details' }}</button>
                        </div>

                        @if ($selectedLogId === $log->id)
                            <div class="border-t border-slate-100 bg-slate-50 p-5 sm:p-6">
                                <div class="grid gap-6 lg:grid-cols-2">
                                    <section>
                                        <div class="flex items-center gap-2"><span
                                                class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                            <h4 class="text-sm font-bold text-slate-900">Event metadata</h4>
                                        </div>@if (!empty($log->metadata))
                                            <pre
                                                class="mt-3 max-h-80 overflow-auto rounded-2xl bg-slate-950 p-4 text-xs leading-6 text-emerald-300 shadow-inner">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        @else<p
                                            class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-500">
                                        No metadata was recorded.</p>@endif
                                    </section>
                                    <section>
                                        <div class="flex items-center gap-2"><span
                                                class="h-2 w-2 rounded-full bg-cyan-500"></span>
                                            <h4 class="text-sm font-bold text-slate-900">User agent</h4>
                                        </div>
                                        <p
                                            class="mt-3 break-words rounded-2xl border border-slate-200 bg-white p-4 font-mono text-xs leading-6 text-slate-600">
                                            {{ $log->user_agent ?? 'Not recorded' }}</p>
                                    </section>
                                </div>
                            </div>
                        @endif
                    </article>
                </div>
            @empty
                <div
                    class="relative z-10 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 6h9.75M10.5 12h9.75m-9.75 6h9.75M3.75 6H4.5v.75h-.75V6Z" />
                        </svg></div>
                    <h2 class="mt-4 text-lg font-bold text-slate-900">No activity found</h2>
                    <p class="mt-2 text-sm text-slate-500">No audit records match the current filters.</p><button
                        type="button" wire:click="resetFilters"
                        class="mt-5 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Clear
                        filters</button>
                </div>
            @endforelse
        </div>

        @if ($logs->hasPages())
        <div class="mt-8">{{ $logs->links() }}</div>@endif
    </main>
</div>