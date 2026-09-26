<div class="min-h-screen bg-slate-50 pb-16">
    <x-page-header eyebrow="Admin workspace" title="Manage users"
        subtitle="Monitor customer and provider accounts, review their platform activity, and control account access.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:self-auto"><span
                    aria-hidden="true">←</span> Dashboard</a>
        </x-slot:actions>
    </x-page-header>

    <main class="relative z-10 mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        @if (session('success'))
            <div role="alert"
                class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>{{ session('success') }}</div>
        @endif
        @error('action')
            <div role="alert"
                class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 shadow-sm">
        {{ $message }}</div>@enderror

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @php
                $metricCards = [
                    ['Total users', $metrics['total'], 'bg-slate-100 text-slate-700', 'All managed accounts'],
                    ['Customers', $metrics['customers'], 'bg-blue-50 text-blue-700', 'Booking customers'],
                    ['Providers', $metrics['providers'], 'bg-violet-50 text-violet-700', 'Service providers'],
                    ['Active', $metrics['active'], 'bg-emerald-50 text-emerald-700', 'Can access BookEase'],
                    ['Suspended', $metrics['suspended'], 'bg-rose-50 text-rose-700', 'Access is restricted'],
                ];
            @endphp
            @foreach ($metricCards as [$label, $count, $classes, $caption])
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-slate-600">{{ $label }}</p><span
                            class="h-3 w-3 rounded-full {{ str($classes)->before(' text-') }}"></span>
                    </div>
                    <p class="mt-3 text-3xl font-black {{ str($classes)->after('text-')->prepend('text-') }}">
                        {{ number_format($count) }}</p>
                    <p class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-500">{{ $caption }}</p>
                </article>
            @endforeach
        </section>

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Account directory</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $users->total() }}
                        {{ \Illuminate\Support\Str::plural('user', $users->total()) }} found</h2>
                </div>
                <div class="grid flex-1 gap-3 sm:grid-cols-2 lg:max-w-4xl lg:grid-cols-[minmax(0,1fr)_11rem_11rem]">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="user-search"
                            class="text-xs font-bold uppercase tracking-wide text-slate-500">Search</label>
                        <div class="relative mt-1"><svg
                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-4.35-4.35m1.6-5.4a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                            </svg><input id="user-search" type="search" wire:model.live.debounce.400ms="search"
                                placeholder="Search by name or email"
                                class="block w-full rounded-xl border-slate-300 py-2.5 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div><label for="role-filter"
                            class="text-xs font-bold uppercase tracking-wide text-slate-500">Role</label><select
                            id="role-filter" wire:model.live="roleFilter"
                            class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All roles</option>@foreach ($roleOptions as $role)
                            <option value="{{ $role->value }}">{{ ucfirst($role->value) }}</option>@endforeach
                        </select></div>
                    <div><label for="user-status-filter"
                            class="text-xs font-bold uppercase tracking-wide text-slate-500">Status</label><select
                            id="user-status-filter" wire:model.live="statusFilter"
                            class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All statuses</option>@foreach ($statusOptions as $status)
                            <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>@endforeach
                        </select></div>
                </div>
            </div>
            @if ($search !== '' || $roleFilter !== '' || $statusFilter !== '')
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                    <p class="text-xs text-slate-500">Filters are currently applied</p><button type="button"
                        wire:click="resetFilters" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Clear all
                        filters</button>
            </div>@endif
        </section>

        <div wire:loading wire:target="search,roleFilter,statusFilter,resetFilters"
            class="mt-4 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">Updating users...</div>

        <div class="mt-5 space-y-5">
            @forelse ($users as $user)
                @php
                    $statusClasses = match ($user->status->value) { 'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'suspended' => 'bg-rose-50 text-rose-700 ring-rose-200', default => 'bg-slate-100 text-slate-600 ring-slate-200'};
                    $roleClasses = match ($user->role->value) { 'customer' => 'bg-blue-50 text-blue-700 ring-blue-200', 'provider' => 'bg-violet-50 text-violet-700 ring-violet-200', default => 'bg-slate-100 text-slate-600 ring-slate-200'};
                    $avatarClasses = $user->role->value === 'provider' ? 'from-violet-500 to-indigo-700 shadow-violet-200' : 'from-blue-500 to-cyan-600 shadow-blue-200';
                    $initials = str($user->name)->explode(' ')->filter()->take(2)->map(fn($part) => str($part)->substr(0, 1))->join('');
                @endphp
                <article wire:key="admin-user-{{ $user->id }}"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                    <header
                        class="flex flex-col gap-5 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div class="flex min-w-0 items-center gap-4">
                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-sm font-black uppercase text-white shadow-md {{ $avatarClasses }}">
                                {{ $initials }}</div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate text-xl font-bold text-slate-900">{{ $user->name }}</h3><span
                                        class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $roleClasses }}">{{ ucfirst($user->role->value) }}</span><span
                                        class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $statusClasses }}">{{ ucfirst($user->status->value) }}</span>
                                </div>
                                <p class="mt-1 break-all text-sm text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            @if ($user->status === \App\Enums\UserStatus::Active)
                                <button type="button" wire:click="openSuspendForm({{ $user->id }})"
                                    class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-50">Suspend
                                    account</button>
                            @else
                                <button type="button" wire:click="reactivateUser({{ $user->id }})"
                                    wire:confirm="Reactivate this user account?" wire:loading.attr="disabled"
                                    class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-100 hover:bg-emerald-700 disabled:opacity-50">Reactivate</button>
                            @endif
                        </div>
                    </header>

                    <div class="p-5 sm:p-6">
                        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Joined</dt>
                                <dd class="mt-2 font-bold text-slate-900">{{ $user->created_at->format('d M Y') }}</dd>
                                <dd class="mt-1 text-xs text-slate-500">{{ $user->created_at->diffForHumans() }}</dd>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Email verification</dt>
                                <dd
                                    class="mt-2 inline-flex items-center gap-2 font-bold {{ $user->hasVerifiedEmail() ? 'text-emerald-700' : 'text-amber-700' }}">
                                    <span
                                        class="h-2 w-2 rounded-full {{ $user->hasVerifiedEmail() ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>{{ $user->hasVerifiedEmail() ? 'Verified' : 'Not verified' }}
                                </dd>
                            </div>
                            @if ($user->role === \App\Enums\UserRole::Customer)
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Bookings</dt>
                                    <dd class="mt-2 text-2xl font-black text-slate-900">{{ $user->bookings_count }}</dd>
                                    <dd class="mt-1 text-xs text-slate-500">Appointments created</dd>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Account type</dt>
                                    <dd class="mt-2 font-bold text-slate-900">Customer</dd>
                                    <dd class="mt-1 text-xs text-slate-500">Can browse and book services</dd>
                                </div>
                            @elseif ($user->role === \App\Enums\UserRole::Provider)
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Business</dt>
                                    <dd class="mt-2 truncate font-bold text-slate-900">
                                        {{ $user->business?->name ?? 'Not created' }}</dd>@if ($user->business)
                                            <dd class="mt-1 text-xs font-semibold text-slate-500">
                                        {{ ucfirst($user->business->status->value) }}</dd>@endif
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Services</dt>
                                    <dd class="mt-2 text-2xl font-black text-slate-900">
                                        {{ $user->business?->services_count ?? 0 }}</dd>
                                    <dd class="mt-1 text-xs text-slate-500">Services created</dd>
                                </div>
                            @endif
                        </dl>

                        @if ($selectedUserId === $user->id && $actionType === 'suspend')
                            <form wire:submit="suspendUser" class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-5">
                                <div class="flex items-start gap-3"><span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700"><svg
                                            class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                        </svg></span>
                                    <div>
                                        <h4 class="font-bold text-rose-900">Suspend {{ $user->name }}?</h4>
                                        <p class="mt-1 text-sm text-rose-700">This user will be blocked from protected areas of
                                            the application.</p>
                                    </div>
                                </div>
                                <label for="reason-{{ $user->id }}"
                                    class="mt-4 block text-sm font-bold text-rose-900">Suspension reason</label>
                                <textarea id="reason-{{ $user->id }}" rows="4" maxlength="1000" wire:model.blur="reason"
                                    placeholder="Explain why this account is being suspended..."
                                    class="mt-2 block w-full rounded-xl border-rose-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                                @error('reason')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>@enderror
                                <div class="mt-4 flex justify-end gap-3"><button type="button" wire:click="closeActionForm"
                                        class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Close</button><button
                                        type="submit" wire:loading.attr="disabled" wire:target="suspendUser"
                                        class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700 disabled:opacity-50"><span
                                            wire:loading.remove wire:target="suspendUser">Confirm suspension</span><span
                                            wire:loading wire:target="suspendUser">Suspending...</span></button></div>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479M15 19.128A12.318 12.318 0 0 1 8.624 21a12.31 12.31 0 0 1-6.374-1.766 6.375 6.375 0 0 1 12.964-3.179M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Z" />
                        </svg></div>
                    <h2 class="mt-4 text-lg font-bold text-slate-900">No users found</h2>
                    <p class="mt-2 text-sm text-slate-500">No user accounts match the current filters.</p>
                    @if ($search !== '' || $roleFilter !== '' || $statusFilter !== '')<button type="button"
                        wire:click="resetFilters"
                        class="mt-5 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Clear
                    filters</button>@endif
                </div>
            @endforelse
        </div>

        @if ($users->hasPages())
        <div class="mt-8">{{ $users->links() }}</div>@endif
    </main>
</div>