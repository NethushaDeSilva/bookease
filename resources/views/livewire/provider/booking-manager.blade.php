<div class="min-h-screen bg-slate-50 pb-16">
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-indigo-800">
        <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 18% 18%, rgba(34,211,238,.35), transparent 28%), radial-gradient(circle at 85% 10%, rgba(129,140,248,.4), transparent 24%);"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-200">
                    <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                    Provider workspace
                </div>
                <h1 class="mt-5 text-3xl font-bold tracking-tight text-white sm:text-4xl">Bookings</h1>
                <p class="mt-3 max-w-xl text-base leading-7 text-indigo-100">
                    Review appointment requests, manage their progress, and keep customers informed.
                </p>
            </div>
        </div>
    </section>

    <main class="relative z-10 mx-auto -mt-5 max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="alert" class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                {{ session('success') }}
            </div>
        @endif

        @if (! $hasBusiness)
            <section class="rounded-3xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                </div>
                <h2 class="mt-5 text-2xl font-bold text-slate-900">Create your business profile first</h2>
                <p class="mx-auto mt-2 max-w-md text-slate-600">You need an active business profile before you can receive and manage customer bookings.</p>
                <a href="{{ route('provider.business.profile') }}" class="mt-7 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">Create business profile</a>
            </section>
        @else
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Booking centre</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">
                            {{ $bookings->total() }} {{ \Illuminate\Support\Str::plural('booking', $bookings->total()) }} found
                        </h2>
                    </div>

                    <div class="grid flex-1 gap-3 sm:grid-cols-2 lg:max-w-4xl lg:grid-cols-4">
                        <div class="sm:col-span-2">
                            <label for="provider-booking-search" class="text-xs font-bold uppercase tracking-wide text-slate-500">Search</label>
                            <div class="relative mt-1">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.6-5.4a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" /></svg>
                                <input id="provider-booking-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Reference, customer or service" class="block w-full rounded-xl border-slate-300 py-2.5 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div>
                            <label for="provider-status-filter" class="text-xs font-bold uppercase tracking-wide text-slate-500">Status</label>
                            <select id="provider-status-filter" wire:model.live="statusFilter" class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All statuses</option>
                                @foreach ($statusOptions as $status)
                                    <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="provider-date-filter" class="text-xs font-bold uppercase tracking-wide text-slate-500">Date</label>
                            <input id="provider-date-filter" type="date" wire:model.live="dateFilter" class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('dateFilter') <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                @if ($search !== '' || $statusFilter !== '' || $dateFilter !== '')
                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                        <p class="text-xs font-medium text-slate-500">Filters are currently applied</p>
                        <button type="button" wire:click="resetFilters" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Clear all filters</button>
                    </div>
                @endif
            </section>

            <div wire:loading wire:target="search,statusFilter,dateFilter,resetFilters" class="mt-4 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">Updating bookings...</div>

            <div class="mt-5 space-y-5">
                @forelse ($bookings as $booking)
                    @php
                        $statusClasses = match ($booking->status->value) {
                            'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
                            'confirmed' => 'bg-blue-50 text-blue-700 ring-blue-200',
                            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                            'cancelled' => 'bg-slate-100 text-slate-600 ring-slate-200',
                            'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
                            default => 'bg-slate-100 text-slate-600 ring-slate-200',
                        };
                        $appointmentIsPast = $booking->slot->ends_at->isPast();
                    @endphp

                    <article wire:key="provider-booking-{{ $booking->id }}" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                        <div class="grid lg:grid-cols-[13rem_1fr]">
                            <div class="border-b border-slate-100 bg-slate-50 p-5 lg:border-b-0 lg:border-r lg:p-6">
                                <div class="flex items-center gap-4 lg:block">
                                    <div class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                                        <span class="text-xs font-bold uppercase tracking-wide">{{ $booking->slot->starts_at->format('M') }}</span>
                                        <span class="text-2xl font-black leading-none">{{ $booking->slot->starts_at->format('d') }}</span>
                                    </div>
                                    <div class="lg:mt-5">
                                        <p class="font-bold text-slate-900">{{ $booking->slot->starts_at->format('l') }}</p>
                                        <p class="mt-1 text-sm font-semibold text-indigo-600">{{ $booking->slot->starts_at->format('h:i A') }}</p>
                                        <p class="text-xs text-slate-500">until {{ $booking->slot->ends_at->format('h:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <header class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between sm:p-6">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="truncate text-xl font-bold text-slate-900">{{ $booking->service->name }}</h3>
                                            <span class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $statusClasses }}">{{ ucfirst($booking->status->value) }}</span>
                                            @if ($appointmentIsPast)
                                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Past appointment</span>
                                            @endif
                                        </div>
                                        <p class="mt-2 text-xs font-medium uppercase tracking-wide text-slate-400">Reference <span class="ml-1 text-slate-600">{{ $booking->booking_reference }}</span></p>
                                    </div>
                                    <div class="shrink-0 sm:text-right">
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Booking value</p>
                                        <p class="mt-1 text-xl font-black text-slate-900">Rs. {{ number_format((float) $booking->price, 2) }}</p>
                                    </div>
                                </header>

                                <div class="p-5 sm:p-6">
                                    <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Customer</dt>
                                            <dd class="mt-2 font-bold text-slate-900">{{ $booking->customer->name }}</dd>
                                            <dd class="mt-0.5 break-all text-sm text-slate-500">{{ $booking->customer->email }}</dd>
                                        </div>
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Appointment</dt>
                                            <dd class="mt-2 font-bold text-slate-900">{{ $booking->slot->starts_at->format('D, d M Y') }}</dd>
                                            <dd class="mt-0.5 text-sm text-slate-500">{{ $booking->slot->starts_at->format('h:i A') }} – {{ $booking->slot->ends_at->format('h:i A') }}</dd>
                                        </div>
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Duration</dt>
                                            <dd class="mt-2 font-bold text-slate-900">{{ $booking->service->duration_minutes }} minutes</dd>
                                            <dd class="mt-0.5 text-sm text-slate-500">Scheduled service time</dd>
                                        </div>
                                    </dl>

                                    @if ($booking->notes)
                                        <div class="mt-4 rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4">
                                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">Customer notes</p>
                                            <p class="mt-2 text-sm leading-6 text-slate-700">{{ $booking->notes }}</p>
                                        </div>
                                    @endif

                                    @if ($booking->cancellation_reason)
                                        <div class="mt-4 rounded-2xl border border-rose-100 bg-rose-50 p-4">
                                            <p class="text-xs font-bold uppercase tracking-wide text-rose-700">Cancellation reason</p>
                                            <p class="mt-2 text-sm leading-6 text-rose-700">{{ $booking->cancellation_reason }}</p>
                                        </div>
                                    @endif

                                    @if ($booking->status === \App\Enums\BookingStatus::Pending || $booking->status === \App\Enums\BookingStatus::Confirmed)
                                        <div class="mt-5 flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                                            @if ($booking->status === \App\Enums\BookingStatus::Pending)
                                                <button type="button" wire:click="confirmBooking({{ $booking->id }})" wire:confirm="Confirm this booking?" wire:loading.attr="disabled" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-100 hover:bg-emerald-700 disabled:opacity-50">Confirm booking</button>
                                                <button type="button" wire:click="openReasonForm({{ $booking->id }}, 'reject')" class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-50">Reject</button>
                                            @else
                                                <button type="button" wire:click="completeBooking({{ $booking->id }})" wire:confirm="Mark this booking as completed?" wire:loading.attr="disabled" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-indigo-100 hover:bg-indigo-700 disabled:opacity-50">Mark completed</button>
                                                <button type="button" wire:click="openReasonForm({{ $booking->id }}, 'cancel')" class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-50">Cancel booking</button>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($selectedBookingId === $booking->id && in_array($actionType, ['reject', 'cancel'], true))
                                        <form wire:submit="submitReasonAction" class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-5">
                                            <div class="flex items-start gap-3">
                                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-rose-900">{{ $actionType === 'reject' ? 'Reject booking' : 'Cancel booking' }}</h4>
                                                    <p class="mt-1 text-sm text-rose-700">Enter a clear reason to record in the booking history.</p>
                                                </div>
                                            </div>
                                            <label for="reason-{{ $booking->id }}" class="mt-4 block text-sm font-bold text-rose-900">Reason</label>
                                            <textarea id="reason-{{ $booking->id }}" rows="4" maxlength="1000" wire:model.blur="reason" placeholder="Explain why this booking is being {{ $actionType === 'reject' ? 'rejected' : 'cancelled' }}..." class="mt-2 block w-full rounded-xl border-rose-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                                            @error('reason') <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p> @enderror
                                            <div class="mt-4 flex justify-end gap-3">
                                                <button type="button" wire:click="closeReasonForm" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Close</button>
                                                <button type="submit" wire:loading.attr="disabled" wire:target="submitReasonAction" class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700 disabled:opacity-50">
                                                    <span wire:loading.remove wire:target="submitReasonAction">Confirm {{ $actionType }}</span>
                                                    <span wire:loading wire:target="submitReasonAction">Updating...</span>
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5M8.25 10.5h7.5m-7.5 3.75h3m-6.75 6h15a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25h-15A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25Z" /></svg></div>
                        <h2 class="mt-4 text-lg font-bold text-slate-900">No bookings found</h2>
                        <p class="mt-2 text-sm text-slate-500">No customer bookings match your current filters.</p>
                        @if ($search !== '' || $statusFilter !== '' || $dateFilter !== '')
                            <button type="button" wire:click="resetFilters" class="mt-5 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Clear filters</button>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($bookings->hasPages())
                <div class="mt-8">{{ $bookings->links() }}</div>
            @endif
        @endif
    </main>
</div>