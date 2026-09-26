<div class="min-h-screen bg-slate-50 pb-16" wire:poll.30s>
    <x-page-header eyebrow="Live platform overview" title="Administration dashboard"
        subtitle="Monitor platform health, manage users and businesses, and review booking activity from one place.">
        <x-slot:actions>
            <div
                class="inline-flex items-center gap-2 self-start rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-medium text-indigo-700 lg:self-auto">
                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Automatically refreshes every 30 seconds
            </div>
        </x-slot:actions>
    </x-page-header>

    <main class="relative z-10 mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Total users</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($userMetrics['total']) }}
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766v-.106a6.375 6.375 0 0 1 11.964-3.073M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg></div>
                </div>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs"><span
                        class="font-bold text-emerald-600">{{ number_format($userMetrics['active']) }}
                        active</span><span class="text-slate-500">{{ number_format($userMetrics['suspended']) }}
                        suspended</span></div>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Businesses</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">
                            {{ number_format($businessMetrics['total']) }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg></div>
                </div>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs"><span
                        class="font-bold text-amber-600">{{ number_format($businessMetrics['pending']) }} awaiting
                        approval</span><a href="{{ route('admin.businesses.index') }}"
                        class="font-bold text-indigo-600 hover:text-indigo-800">Review</a></div>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Total bookings</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($bookingMetrics['total']) }}
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700"><svg
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3.75 18.75V7.5A2.25 2.25 0 0 1 6 5.25h12a2.25 2.25 0 0 1 2.25 2.25v11.25m-16.5 0A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25m-16.5 0v-7.5h16.5v7.5" />
                        </svg></div>
                </div>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs"><span
                        class="font-bold text-indigo-600">{{ number_format($bookingMetrics['upcoming_active']) }}
                        upcoming</span><span class="text-slate-500">{{ number_format($bookingMetrics['today']) }}
                        today</span></div>
            </article>

            <article
                class="rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-700 p-6 text-white shadow-lg shadow-indigo-200">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-100">Completed value</p>
                        <p class="mt-2 text-3xl font-black">Rs.
                            {{ number_format($bookingMetrics['completed_value'], 2) }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15"><svg class="h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.22 12.768 12 12 12c-.725 0-1.45-.22-2.004-.659-1.107-.879-1.107-2.303 0-3.182s2.901-.879 4.008 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg></div>
                </div>
                <p class="mt-5 border-t border-white/15 pt-4 text-xs text-indigo-100">Revenue represented by completed
                    appointments</p>
            </article>
        </section>

        <section class="mt-6 grid gap-6 lg:grid-cols-[1.3fr_.7fr]">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Operations</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Quick management</h2>
                    </div>
                </div>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.users.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-indigo-50/50">
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><svg
                                class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.205-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.941 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg></span>
                        <span class="min-w-0 flex-1"><span class="block font-bold text-slate-900">Manage
                                users</span><span class="text-sm text-slate-500">Roles and account
                                status</span></span><span
                            class="text-indigo-500 transition group-hover:translate-x-1">→</span>
                    </a>
                    <a href="{{ route('admin.businesses.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-violet-200 hover:bg-violet-50/50">
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><svg
                                class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg></span>
                        <span class="min-w-0 flex-1"><span class="block font-bold text-slate-900">Manage
                                businesses</span><span class="text-sm text-slate-500">Approvals and
                                status</span></span><span
                            class="text-violet-500 transition group-hover:translate-x-1">→</span>
                    </a>
                    <a href="{{ route('admin.bookings.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-200 hover:bg-cyan-50/50">
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700"><svg
                                class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3.75 18.75V7.5A2.25 2.25 0 0 1 6 5.25h12a2.25 2.25 0 0 1 2.25 2.25v11.25m-16.5 0A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25m-16.5 0v-7.5h16.5v7.5" />
                            </svg></span>
                        <span class="min-w-0 flex-1"><span class="block font-bold text-slate-900">Monitor
                                bookings</span><span class="text-sm text-slate-500">Platform
                                appointments</span></span><span
                            class="text-cyan-600 transition group-hover:translate-x-1">→</span>
                    </a>
                    <a href="{{ route('admin.activity.index') }}"
                        class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-emerald-200 hover:bg-emerald-50/50">
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><svg
                                class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 6h9.75M10.5 12h9.75m-9.75 6h9.75M3.75 6H4.5v.75h-.75V6Zm0 6h.75v.75h-.75V12Zm0 6h.75v.75h-.75V18Z" />
                            </svg></span>
                        <span class="min-w-0 flex-1"><span class="block font-bold text-slate-900">Activity
                                logs</span><span class="text-sm text-slate-500">Audit platform
                                actions</span></span><span
                            class="text-emerald-600 transition group-hover:translate-x-1">→</span>
                    </a>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Accounts</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Users by role</h2>
                    </div><a href="{{ route('admin.users.index') }}"
                        class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View all</a>
                </div>
                @php $maxRoleCount = max(1, $userMetrics['customers'], $userMetrics['providers'], $userMetrics['admins']); @endphp
                <div class="mt-6 space-y-5">
                    @foreach ([['Customers', $userMetrics['customers'], 'bg-indigo-500'], ['Providers', $userMetrics['providers'], 'bg-cyan-500'], ['Administrators', $userMetrics['admins'], 'bg-violet-500']] as [$label, $count, $bar])
                        <div>
                            <div class="mb-2 flex justify-between text-sm"><span
                                    class="font-semibold text-slate-600">{{ $label }}</span><span
                                    class="font-black text-slate-900">{{ number_format($count) }}</span></div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full {{ $bar }}"
                                    style="width: {{ ($count / $maxRoleCount) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Booking lifecycle</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">Status overview</h2>
                </div>
                <p class="text-sm font-semibold text-slate-500"><span
                        class="text-indigo-600">{{ number_format($bookingMetrics['today']) }}</span> appointments today
                </p>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($bookingStatusCounts as $status => $count)
                    @php
                        [$statusClasses, $dotClass] = match ($status) {
                            'pending' => ['border-amber-200 bg-amber-50 text-amber-800', 'bg-amber-500'],
                            'confirmed' => ['border-blue-200 bg-blue-50 text-blue-800', 'bg-blue-500'],
                            'completed' => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'bg-emerald-500'],
                            'cancelled' => ['border-slate-200 bg-slate-50 text-slate-700', 'bg-slate-500'],
                            'rejected' => ['border-rose-200 bg-rose-50 text-rose-800', 'bg-rose-500'],
                            default => ['border-slate-200 bg-slate-50 text-slate-700', 'bg-slate-500'],
                        };
                    @endphp
                    <article class="rounded-2xl border p-4 {{ $statusClasses }}">
                        <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full {{ $dotClass }}"></span>
                            <p class="text-sm font-bold">{{ ucfirst($status) }}</p>
                        </div>
                        <p class="mt-3 text-3xl font-black">{{ number_format($count) }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Latest activity</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">Recent bookings</h2>
                </div><a href="{{ route('admin.bookings.index') }}"
                    class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View all bookings →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>@foreach (['Reference', 'Customer', 'Service', 'Appointment', 'Status'] as $heading)
                            <th
                                class="whitespace-nowrap px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        {{ $heading }}</th>@endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($recentBookings as $booking)
                            @php
                                $bookingStatusClass = match ($booking->status->value) {
                                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-200', 'confirmed' => 'bg-blue-50 text-blue-700 ring-blue-200', 'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'cancelled' => 'bg-slate-100 text-slate-600 ring-slate-200', 'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200', default => 'bg-slate-100 text-slate-600 ring-slate-200',
                                };
                            @endphp
                            <tr wire:key="admin-booking-{{ $booking->id }}" class="hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-indigo-700">
                                    {{ $booking->booking_reference }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-900">{{ $booking->customer->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $booking->customer->email }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-800">{{ $booking->service->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $booking->service->business->name }}</p>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    {{ $booking->slot->starts_at->format('d M Y, h:i A') }}</td>
                                <td class="whitespace-nowrap px-6 py-4"><span
                                        class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $bookingStatusClass }}">{{ ucfirst($booking->status->value) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">No bookings have been
                                    created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-6 grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Business health</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Business status</h2>
                    </div><a href="{{ route('admin.businesses.index') }}"
                        class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Manage</a>
                </div>
                <div class="mt-5 space-y-3">
                    @foreach ([['Pending approval', $businessMetrics['pending'], 'bg-amber-50 text-amber-700', 'bg-amber-500'], ['Active businesses', $businessMetrics['active'], 'bg-emerald-50 text-emerald-700', 'bg-emerald-500'], ['Suspended', $businessMetrics['suspended'], 'bg-rose-50 text-rose-700', 'bg-rose-500']] as [$label, $count, $classes, $dot])
                        <div class="flex items-center justify-between rounded-2xl p-4 {{ $classes }}"><span
                                class="flex items-center gap-2 text-sm font-bold"><span
                                    class="h-2.5 w-2.5 rounded-full {{ $dot }}"></span>{{ $label }}</span><span
                                class="text-xl font-black">{{ number_format($count) }}</span></div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Audit trail</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Recent activity</h2>
                    </div><a href="{{ route('admin.activity.index') }}"
                        class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View logs</a>
                </div>
                <div class="mt-4 divide-y divide-slate-100">
                    @forelse ($recentActivity->take(6) as $activity)
                        <div wire:key="admin-activity-{{ $activity->id }}" class="flex items-center gap-3 py-3.5">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-indigo-400"></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800">
                                    {{ str($activity->action)->replace('.', ' ')->title() }}</p>
                                <p class="truncate text-xs text-slate-500">
                                    {{ $activity->user?->name ?? 'System or deleted user' }}</p>
                            </div>
                            <time
                                class="shrink-0 text-xs text-slate-400">{{ $activity->created_at->diffForHumans() }}</time>
                        </div>
                    @empty
                        <p class="py-10 text-center text-sm text-slate-500">No activity has been recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</div>