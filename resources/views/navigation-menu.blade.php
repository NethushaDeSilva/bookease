@php
    $currentUser = Auth::user();
    $homeUrl = $currentUser->isCustomer()
        ? route('customer.services.index')
        : ($currentUser->isProvider() ? route('provider.services.index') : route('dashboard'));
    $roleLabel = $currentUser->isAdmin() ? 'Administrator' : ($currentUser->isProvider() ? 'Service Provider' : 'Customer');
    $initials = str($currentUser->name)->explode(' ')->filter()->take(2)->map(fn ($part) => str($part)->substr(0, 1))->join('');

    $navigationItems = $currentUser->isAdmin()
        ? [
            ['Dashboard', route('dashboard'), 'dashboard'],
            ['Businesses', route('admin.businesses.index'), 'admin.businesses.*'],
            ['Users', route('admin.users.index'), 'admin.users.*'],
            ['Bookings', route('admin.bookings.index'), 'admin.bookings.*'],
        ]
        : ($currentUser->isProvider()
            ? [
                ['Services', route('provider.services.index'), 'provider.services.*'],
                ['Availability', route('provider.availability.index'), 'provider.availability.*'],
                ['Bookings', route('provider.bookings.index'), 'provider.bookings.*'],
                ['My Business', route('provider.business.profile'), 'provider.business.*'],
            ]
            : [
                ['Services', route('customer.services.index'), 'customer.services.*'],
                ['My Bookings', route('customer.bookings.index'), 'customer.bookings.*'],
            ]);
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm shadow-slate-200/40">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-[4.5rem] items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-7 lg:gap-10">
                <a href="{{ $homeUrl }}" class="group flex shrink-0 items-center gap-3" aria-label="BookEase home">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 text-white shadow-lg shadow-indigo-500/25 transition group-hover:-translate-y-0.5 group-hover:shadow-indigo-500/35">
                        <x-application-mark class="h-5 w-5" />
                    </span>
                    <span class="hidden text-lg font-extrabold leading-none tracking-tight text-slate-950 sm:block">BookEase</span>
                </a>

                <div class="hidden items-center gap-1 md:flex">
                    @foreach ($navigationItems as [$label, $url, $pattern])
                        @php $isActive = request()->routeIs($pattern); @endphp
                        <a href="{{ $url }}"
                            class="relative rounded-xl px-3.5 py-2.5 text-sm font-semibold transition {{ $isActive ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' }}">
                            {{ __($label) }}
                            @if ($isActive)
                                <span class="absolute inset-x-4 -bottom-[0.83rem] h-0.5 rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <x-dropdown align="right" width="60">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                {{ $currentUser->currentTeam->name }}
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="w-60">
                                <div class="px-4 py-2 text-xs font-bold uppercase tracking-wide text-slate-400">{{ __('Manage Team') }}</div>
                                <x-dropdown-link href="{{ route('teams.show', $currentUser->currentTeam->id) }}">{{ __('Team Settings') }}</x-dropdown-link>
                                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())<x-dropdown-link href="{{ route('teams.create') }}">{{ __('Create New Team') }}</x-dropdown-link>@endcan
                                @if ($currentUser->allTeams()->count() > 1)
                                    <div class="my-1 border-t border-slate-100"></div>
                                    @foreach ($currentUser->allTeams() as $team)<x-switchable-team :team="$team" />@endforeach
                                @endif
                            </div>
                        </x-slot>
                    </x-dropdown>
                @endif

                <x-dropdown align="right" width="60">
                    <x-slot name="trigger">
                        <button type="button" class="group flex items-center gap-3 rounded-2xl border border-transparent px-2 py-1.5 text-left transition hover:border-slate-200 hover:bg-slate-50 focus:outline-none">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="h-9 w-9 rounded-xl object-cover ring-2 ring-indigo-100" src="{{ $currentUser->profile_photo_url }}" alt="{{ $currentUser->name }}">
                            @else
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-xs font-black uppercase text-white shadow-sm">{{ $initials }}</span>
                            @endif
                            <span class="hidden max-w-44 lg:block">
                                <span class="block truncate text-sm font-bold text-slate-800">{{ $currentUser->name }}</span>
                                <span class="block text-[11px] font-semibold text-slate-400">{{ $roleLabel }}</span>
                            </span>
                            <svg class="h-4 w-4 text-slate-400 transition group-hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="w-60">
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="truncate text-sm font-bold text-slate-900">{{ $currentUser->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $currentUser->email }}</p>
                            </div>
                            <div class="py-1">
                                <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile settings') }}</x-dropdown-link>
                                @if ($currentUser->isProvider())
                                    <x-dropdown-link href="{{ route('customer.bookings.index') }}">{{ __('My appointments') }}</x-dropdown-link>
                                @endif
                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())<x-dropdown-link href="{{ route('api-tokens.index') }}">{{ __('API Tokens') }}</x-dropdown-link>@endif
                            </div>
                            <div class="border-t border-slate-100 py-1">
                                <form method="POST" action="{{ route('logout') }}" x-data>@csrf<x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link></form>
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <button type="button" @click="open = ! open" :aria-expanded="open" aria-label="Toggle navigation menu"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 md:hidden">
                <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="open" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition.opacity @click.outside="open = false" class="border-t border-slate-100 bg-white md:hidden">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
            <div class="space-y-1">
                @foreach ($navigationItems as [$label, $url, $pattern])
                    @php $isActive = request()->routeIs($pattern); @endphp
                    <a href="{{ $url }}" @click="open = false" class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition {{ $isActive ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        {{ __($label) }}
                        @if ($isActive)<span class="h-2 w-2 rounded-full bg-indigo-500"></span>@endif
                    </a>
                @endforeach
            </div>

            <div class="mt-4 border-t border-slate-100 pt-4">
                <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="h-11 w-11 rounded-xl object-cover ring-2 ring-white" src="{{ $currentUser->profile_photo_url }}" alt="{{ $currentUser->name }}">
                    @else
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-xs font-black uppercase text-white">{{ $initials }}</span>
                    @endif
                    <div class="min-w-0"><p class="truncate text-sm font-bold text-slate-900">{{ $currentUser->name }}</p><p class="truncate text-xs text-slate-500">{{ $currentUser->email }}</p><p class="mt-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-600">{{ $roleLabel }}</p></div>
                </div>

                <div class="mt-2 space-y-1">
                    <a href="{{ route('profile.show') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-indigo-700">{{ __('Profile settings') }}</a>
                    @if ($currentUser->isProvider())
                        <a href="{{ route('customer.bookings.index') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-indigo-700">{{ __('My appointments') }}</a>
                    @endif
                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())<a href="{{ route('api-tokens.index') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-indigo-700">{{ __('API Tokens') }}</a>@endif
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="block w-full rounded-xl px-4 py-3 text-left text-sm font-bold text-rose-600 hover:bg-rose-50">{{ __('Log Out') }}</button></form>
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="mt-3 border-t border-slate-100 pt-3"><p class="px-4 py-2 text-xs font-bold uppercase tracking-wide text-slate-400">{{ __('Manage Team') }}</p><a href="{{ route('teams.show', $currentUser->currentTeam->id) }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">{{ __('Team Settings') }}</a>@can('create', Laravel\Jetstream\Jetstream::newTeamModel())<a href="{{ route('teams.create') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">{{ __('Create New Team') }}</a>@endcan</div>
                @endif
            </div>
        </div>
    </div>
</nav>
