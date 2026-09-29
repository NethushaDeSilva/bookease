<div class="min-h-screen bg-slate-50 pb-16">
    <x-page-header title="Booking monitor"
        subtitle="Track appointments, booking values, customers, and providers across the entire platform.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:self-auto"><span aria-hidden="true">←</span> Dashboard</a>
        </x-slot:actions>
    </x-page-header>

    <main class="relative z-10 mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Total bookings</p><p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($metrics['total']) }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 18.75V7.5A2.25 2.25 0 0 1 6 5.25h12a2.25 2.25 0 0 1 2.25 2.25v11.25m-16.5 0A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25m-16.5 0v-7.5h16.5v7.5" /></svg></span></div><p class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-500">All appointments created on BookEase</p></article>
            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Appointments today</p><p class="mt-2 text-3xl font-black text-blue-700">{{ number_format($metrics['today']) }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></span></div><p class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-500">Scheduled for the current date</p></article>
            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Upcoming active</p><p class="mt-2 text-3xl font-black text-violet-700">{{ number_format($metrics['upcoming_active']) }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-50 text-violet-600"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg></span></div><p class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-500">Pending or confirmed future bookings</p></article>
            <article class="rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-700 p-6 text-white shadow-lg shadow-indigo-200"><div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-indigo-100">Completed value</p><p class="mt-2 text-3xl font-black">Rs. {{ number_format($metrics['completed_value'], 2) }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.22 12.768 12 12 12c-.725 0-1.45-.22-2.004-.659-1.107-.879-1.107-2.303 0-3.182s2.901-.879 4.008 0l.415.33" /></svg></span></div><p class="mt-5 border-t border-white/15 pt-4 text-xs text-indigo-100">Value of successfully completed bookings</p></article>
        </section>

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div><p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Booking lifecycle</p><h2 class="mt-1 text-xl font-bold text-slate-900">Status overview</h2></div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($statusCounts as $status => $count)
                    @php
                        [$classes, $dot] = match ($status) {
                            'pending' => ['border-amber-200 bg-amber-50 text-amber-800', 'bg-amber-500'], 'confirmed' => ['border-blue-200 bg-blue-50 text-blue-800', 'bg-blue-500'], 'completed' => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'bg-emerald-500'], 'cancelled' => ['border-slate-200 bg-slate-50 text-slate-700', 'bg-slate-500'], 'rejected' => ['border-rose-200 bg-rose-50 text-rose-800', 'bg-rose-500'], default => ['border-slate-200 bg-slate-50 text-slate-700', 'bg-slate-500'],
                        };
                    @endphp
                    <article class="rounded-2xl border p-4 {{ $classes }}"><div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span><p class="text-sm font-bold">{{ ucfirst($status) }}</p></div><p class="mt-3 text-3xl font-black">{{ number_format($count) }}</p></article>
                @endforeach
            </div>
        </section>

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div><p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Platform appointments</p><h2 class="mt-1 text-xl font-bold text-slate-900">{{ $bookings->total() }} {{ \Illuminate\Support\Str::plural('booking', $bookings->total()) }} found</h2></div>
                <div class="grid flex-1 gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:max-w-5xl xl:grid-cols-[minmax(0,1.5fr)_11rem_13rem_10rem_11rem]">
                    <div class="sm:col-span-2 lg:col-span-4 xl:col-span-1"><label for="admin-booking-search" class="text-xs font-bold uppercase tracking-wide text-slate-500">Search</label><div class="relative mt-1"><svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.6-5.4a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" /></svg><input id="admin-booking-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Reference, customer, provider or service" class="block w-full rounded-xl border-slate-300 py-2.5 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div></div>
                    <div><label for="admin-booking-status" class="text-xs font-bold uppercase tracking-wide text-slate-500">Status</label><select id="admin-booking-status" wire:model.live="statusFilter" class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">All statuses</option>@foreach ($statusOptions as $status)<option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>@endforeach</select></div>
                    <div><label for="admin-booking-business" class="text-xs font-bold uppercase tracking-wide text-slate-500">Business</label><select id="admin-booking-business" wire:model.live="businessFilter" class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">All businesses</option>@foreach ($businesses as $business)<option value="{{ $business->id }}">{{ $business->name }}</option>@endforeach</select></div>
                    <div><label for="admin-booking-period" class="text-xs font-bold uppercase tracking-wide text-slate-500">Period</label><select id="admin-booking-period" wire:model.live="periodFilter" class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">All dates</option><option value="upcoming">Upcoming</option><option value="past">Past</option></select></div>
                    <div><label for="admin-booking-date" class="text-xs font-bold uppercase tracking-wide text-slate-500">Exact date</label><input id="admin-booking-date" type="date" wire:model.live="dateFilter" class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">@error('dateFilter')<p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror</div>
                </div>
            </div>
            @if ($search !== '' || $statusFilter !== '' || $businessFilter !== '' || $periodFilter !== '' || $dateFilter !== '')<div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4"><p class="text-xs text-slate-500">Filters are currently applied</p><button type="button" wire:click="resetFilters" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Clear all filters</button></div>@endif
        </section>

        <div wire:loading wire:target="search,statusFilter,businessFilter,periodFilter,dateFilter,resetFilters" class="mt-4 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">Updating bookings...</div>

        <div class="mt-5 space-y-5">
            @forelse ($bookings as $booking)
                @php
                    $statusClasses = match ($booking->status->value) {'pending' => 'bg-amber-50 text-amber-700 ring-amber-200', 'confirmed' => 'bg-blue-50 text-blue-700 ring-blue-200', 'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'cancelled' => 'bg-slate-100 text-slate-600 ring-slate-200', 'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200', default => 'bg-slate-100 text-slate-600 ring-slate-200'};
                    $appointmentIsPast = $booking->slot->ends_at->isPast();
                @endphp
                <article wire:key="admin-monitor-booking-{{ $booking->id }}" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                    <div class="grid lg:grid-cols-[13rem_1fr]">
                        <aside class="border-b border-slate-100 bg-slate-50 p-5 lg:border-b-0 lg:border-r lg:p-6">
                            <div class="flex items-center gap-4 lg:block"><div class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200"><span class="text-xs font-bold uppercase">{{ $booking->slot->starts_at->format('M') }}</span><span class="text-2xl font-black leading-none">{{ $booking->slot->starts_at->format('d') }}</span></div><div class="lg:mt-5"><p class="font-bold text-slate-900">{{ $booking->slot->starts_at->format('l') }}</p><p class="mt-1 text-sm font-semibold text-indigo-600">{{ $booking->slot->starts_at->format('h:i A') }}</p><p class="text-xs text-slate-500">to {{ $booking->slot->ends_at->format('h:i A') }}</p></div></div>
                        </aside>
                        <div>
                            <header class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between sm:p-6">
                                <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h3 class="truncate text-xl font-bold text-slate-900">{{ $booking->service->name }}</h3><span class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $statusClasses }}">{{ ucfirst($booking->status->value) }}</span>@if ($appointmentIsPast)<span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Past</span>@endif</div><p class="mt-2 text-xs font-medium uppercase tracking-wide text-slate-400">Reference <span class="ml-1 text-slate-600">{{ $booking->booking_reference }}</span></p><p class="mt-1 text-xs text-slate-400">Created {{ $booking->created_at->format('d M Y, h:i A') }}</p></div>
                                <div class="shrink-0 sm:text-right"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Booking value</p><p class="mt-1 text-xl font-black text-slate-900">Rs. {{ number_format((float) $booking->price, 2) }}</p></div>
                            </header>
                            <div class="p-5 sm:p-6">
                                <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                    <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Customer</dt><dd class="mt-2 font-bold text-slate-900">{{ $booking->customer->name }}</dd><dd class="mt-0.5 break-all text-sm text-slate-500">{{ $booking->customer->email }}</dd></div>
                                    <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Business</dt><dd class="mt-2 font-bold text-slate-900">{{ $booking->service->business->name }}</dd><dd class="mt-0.5 text-sm text-slate-500">Provider: {{ $booking->service->business->owner->name }}</dd></div>
                                    <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Appointment</dt><dd class="mt-2 font-bold text-slate-900">{{ $booking->slot->starts_at->format('D, d M Y') }}</dd><dd class="mt-0.5 text-sm text-slate-500">{{ $booking->slot->starts_at->format('h:i A') }} – {{ $booking->slot->ends_at->format('h:i A') }}</dd></div>
                                    <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Customer review</dt>@if ($booking->review)<dd class="mt-2 flex items-center gap-2 font-bold text-amber-600"><span>★</span>{{ $booking->review->rating }} / 5</dd><dd class="mt-0.5 text-sm text-slate-500">Review submitted</dd>@else<dd class="mt-2 font-bold text-slate-700">No review</dd><dd class="mt-0.5 text-sm text-slate-500">Not yet reviewed</dd>@endif</div>
                                </dl>
                                @if ($booking->notes)<div class="mt-4 rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4"><p class="text-xs font-bold uppercase tracking-wide text-indigo-600">Customer notes</p><p class="mt-2 text-sm leading-6 text-slate-700">{{ $booking->notes }}</p></div>@endif
                                @if ($booking->cancellation_reason)<div class="mt-4 rounded-2xl border border-rose-100 bg-rose-50 p-4"><p class="text-xs font-bold uppercase tracking-wide text-rose-700">Cancellation or rejection reason</p><p class="mt-2 text-sm leading-6 text-rose-700">{{ $booking->cancellation_reason }}</p></div>@endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center"><div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 18.75V7.5A2.25 2.25 0 0 1 6 5.25h12a2.25 2.25 0 0 1 2.25 2.25v11.25" /></svg></div><h2 class="mt-4 text-lg font-bold text-slate-900">No bookings found</h2><p class="mt-2 text-sm text-slate-500">No bookings match the current filters.</p><button type="button" wire:click="resetFilters" class="mt-5 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Clear filters</button></div>
            @endforelse
        </div>

        @if ($bookings->hasPages())<div class="mt-8">{{ $bookings->links() }}</div>@endif
    </main>
</div>