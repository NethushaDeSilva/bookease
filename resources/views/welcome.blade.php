<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description"
        content="BookEase helps customers discover services and book appointments while providers manage availability and bookings.">

    <title>BookEase — Service booking made simple</title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white font-sans text-slate-900 antialiased">
    <div class="relative min-h-screen overflow-hidden">
        {{-- Background decoration --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div
                class="absolute left-1/2 top-[-22rem] h-[52rem] w-[52rem] -translate-x-1/2 rounded-full bg-indigo-200/60 blur-3xl">
            </div>
        </div>

        {{-- Navigation --}}
        <header class="relative z-50">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8"
                aria-label="Main navigation">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 shadow-lg shadow-indigo-500/25">
                        <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.5 15 2 2 5-5" />
                        </svg>
                    </span>

                    <span>
                        <span class="block text-xl font-extrabold tracking-tight">
                            BookEase
                        </span>

                        <span class="block text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-400">
                            Booking simplified
                        </span>
                    </span>
                </a>

                <div class="hidden items-center gap-8 md:flex">
                    <a href="#features" class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">
                        Features
                    </a>

                    <a href="#how-it-works" class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">
                        How it works
                    </a>

                    <a href="#for-businesses"
                        class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">
                        For businesses
                    </a>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-2 sm:gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-900/15 transition hover:bg-indigo-600 sm:px-5">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-indigo-600 sm:px-4">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-900 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 sm:px-5">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>
        </header>

        <main class="relative z-10">
            {{-- Hero --}}
            <section class="mx-auto max-w-7xl px-6 pb-24 pt-14 lg:px-8 lg:pb-32 lg:pt-24">
                <div class="grid items-center gap-16 lg:grid-cols-[1.02fr_0.98fr]">
                    <div>
                        <div
                            class="mb-7 inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-700 shadow-sm">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-300 opacity-75"></span>

                                <span class="relative inline-flex h-2 w-2 rounded-full bg-cyan-300"></span>
                            </span>

                            One platform. Every appointment.
                        </div>

                        <x-page-header variant="app" bare>
                            <x-slot:title>
                                <span
                                    class="block max-w-3xl text-5xl font-extrabold leading-[1.05] tracking-[-0.04em] text-slate-950 sm:text-6xl lg:text-7xl">
                                    Book services.
                                    <span
                                        class="bg-gradient-to-r from-cyan-600 via-sky-600 to-indigo-600 bg-clip-text text-transparent">
                                        Grow businesses.
                                    </span>
                                </span>
                            </x-slot:title>

                            <x-slot:subtitle>
                                BookEase connects customers with trusted service
                                providers and turns appointment management into a
                                simple, reliable experience.
                            </x-slot:subtitle>
                        </x-page-header>

                        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-7 py-4 text-base font-bold text-white shadow-xl shadow-indigo-500/25 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-indigo-500/30">
                                    Open dashboard

                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                    </svg>
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-cyan-400 px-7 py-4 text-base font-bold text-white shadow-xl shadow-indigo-500/25 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-indigo-500/30">
                                        Start booking free

                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                        </svg>
                                    </a>
                                @endif

                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-7 py-4 text-base font-bold text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50">
                                    Sign in to BookEase
                                </a>
                            @endauth
                        </div>

                        <div class="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-sm font-medium text-slate-600">
                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                </svg>

                                Real-time availability
                            </span>

                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                </svg>

                                Secure accounts
                            </span>

                            <span class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                </svg>

                                Easy cancellation
                            </span>
                        </div>
                    </div>

                    {{-- SaaS product preview --}}
                    <div class="relative">
                        <div
                            class="absolute -inset-6 rounded-[2.5rem] bg-gradient-to-r from-indigo-500/20 to-cyan-400/20 blur-2xl">
                        </div>

                        <div
                            class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white/90 p-3 shadow-2xl shadow-indigo-950/15 backdrop-blur">
                            <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50">
                                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                                        <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                    </div>

                                    <span class="text-xs font-medium text-slate-500">
                                        app.bookease.com
                                    </span>

                                    <span class="w-12"></span>
                                </div>

                                <div class="p-5 sm:p-7">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-300">
                                                Customer portal
                                            </p>

                                            <h2 class="mt-2 text-2xl font-bold text-slate-950">
                                                Find your next service
                                            </h2>
                                        </div>

                                        <span
                                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" aria-hidden="true">
                                                <circle cx="11" cy="11" r="7"></circle>
                                                <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                                            </svg>
                                        </span>
                                    </div>

                                    <div
                                        class="mt-6 flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500 shadow-sm">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" aria-hidden="true">
                                            <circle cx="11" cy="11" r="7"></circle>
                                            <path d="m20 20-3.5-3.5"></path>
                                        </svg>

                                        Search services, businesses or locations
                                    </div>

                                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                            <div
                                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-400/15 text-cyan-300">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 19.5V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v14.5M8 7h7M8 11h7M8 15h4" />
                                                </svg>
                                            </div>

                                            <h3 class="mt-4 font-bold text-slate-900">
                                                Private Tutoring
                                            </h3>

                                            <p class="mt-1 text-xs text-slate-400">
                                                Expert one-to-one learning
                                            </p>

                                            <div class="mt-4 flex items-center justify-between">
                                                <span class="text-sm font-bold text-cyan-300">
                                                    From LKR 2,500
                                                </span>

                                                <span class="rounded-lg bg-slate-100 px-2 py-1 text-xs text-slate-600">
                                                    60 min
                                                </span>
                                            </div>
                                        </article>

                                        <article class="rounded-2xl border border-indigo-400/30 bg-indigo-500/10 p-4">
                                            <div
                                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-400/15 text-indigo-300">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 8h16v11H4zM8 8l1.5-3h5L16 8" />
                                                    <circle cx="12" cy="13.5" r="3"></circle>
                                                </svg>
                                            </div>

                                            <h3 class="mt-4 font-bold text-slate-900">
                                                Photography
                                            </h3>

                                            <p class="mt-1 text-xs text-slate-400">
                                                Professional photo sessions
                                            </p>

                                            <div class="mt-4 flex items-center justify-between">
                                                <span class="text-sm font-bold text-indigo-300">
                                                    From LKR 5,000
                                                </span>

                                                <span
                                                    class="rounded-lg bg-white px-2 py-1 text-xs text-slate-600 shadow-sm">
                                                    90 min
                                                </span>
                                            </div>
                                        </article>
                                    </div>

                                    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">
                                                    Available appointments
                                                </p>

                                                <p class="mt-1 text-xs text-slate-400">
                                                    Wednesday, 19 August
                                                </p>
                                            </div>

                                            <span
                                                class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-bold text-emerald-300">
                                                4 slots
                                            </span>
                                        </div>

                                        <div class="mt-4 grid grid-cols-3 gap-2">
                                            <span
                                                class="rounded-lg border border-cyan-300/40 bg-cyan-300/10 px-3 py-2 text-center text-xs font-bold text-cyan-200">
                                                09:00
                                            </span>

                                            <span
                                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-center text-xs font-semibold text-slate-600">
                                                11:30
                                            </span>

                                            <span
                                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-center text-xs font-semibold text-slate-600">
                                                14:00
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="absolute -bottom-7 -left-6 hidden rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-2xl shadow-indigo-950/15 backdrop-blur sm:block">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-400/15 text-emerald-300">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                    </svg>
                                </span>

                                <div>
                                    <p class="text-sm font-bold text-slate-900">
                                        Booking confirmed
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Your appointment is secured
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Value strip --}}
            <section class="border-y border-slate-200 bg-slate-50/80">
                <div class="mx-auto grid max-w-7xl gap-8 px-6 py-10 sm:grid-cols-2 lg:px-8">
                    <div class="text-center sm:text-left">
                        <p class="text-3xl font-extrabold text-slate-950">
                            24/7
                        </p>

                        <p class="mt-1 text-sm text-slate-400">
                            Online appointment access
                        </p>
                    </div>

                    <div class="text-center sm:text-left">
                        <p class="text-3xl font-extrabold text-slate-950">
                            Real-time
                        </p>

                        <p class="mt-1 text-sm text-slate-400">
                            Availability and booking updates
                        </p>
                    </div>
                </div>
            </section>

            {{-- Features --}}
            <section id="features" class="mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">
                <div class="max-w-3xl">
                    <p class="text-sm font-bold uppercase tracking-[0.24em] text-cyan-300">
                        Everything in one place
                    </p>

                    <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">
                        A better booking experience for everyone.
                    </h2>

                    <p class="mt-5 text-lg leading-8 text-slate-600">
                        From discovering a service to managing every appointment,
                        BookEase keeps the complete workflow connected.
                    </p>
                </div>

                <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @php
                        $features = [
                            [
                                'title' => 'Discover services',
                                'description' =>
                                    'Search active services, compare providers and find the right option quickly.',
                                'color' => 'cyan',
                            ],
                            [
                                'title' => 'Live availability',
                                'description' =>
                                    'See upcoming appointment times and remaining capacity before booking.',
                                'color' => 'indigo',
                            ],
                            [
                                'title' => 'Secure bookings',
                                'description' =>
                                    'Book confidently with protected accounts and conflict-prevention rules.',
                                'color' => 'emerald',
                            ],
                            [
                                'title' => 'Business management',
                                'description' =>
                                    'Providers manage profiles, services, pricing and availability from one workspace.',
                                'color' => 'violet',
                            ],
                            [
                                'title' => 'Booking workflow',
                                'description' =>
                                    'Confirm, complete, reject or cancel bookings with a clear status history.',
                                'color' => 'sky',
                            ],
                            [
                                'title' => 'Trusted reviews',
                                'description' =>
                                    'Customers can review completed appointments and help others choose confidently.',
                                'color' => 'amber',
                            ],
                        ];
                    @endphp

                    @foreach ($features as $feature)
                        <article
                            class="group rounded-3xl border border-slate-200 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-950/10">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-{{ $feature['color'] }}-400/15 text-{{ $feature['color'] }}-300">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                </svg>
                            </div>

                            <h3 class="mt-6 text-xl font-bold text-slate-900">
                                {{ $feature['title'] }}
                            </h3>

                            <p class="mt-3 leading-7 text-slate-600">
                                {{ $feature['description'] }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- How it works --}}
            <section id="how-it-works" class="border-y border-slate-200 bg-gradient-to-b from-slate-50 to-white">
                <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">
                    <div class="text-center">
                        <p class="text-sm font-bold uppercase tracking-[0.24em] text-indigo-300">
                            Simple by design
                        </p>

                        <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">
                            Book in three easy steps.
                        </h2>
                    </div>

                    <div class="mt-16 grid gap-8 md:grid-cols-3">
                        @foreach ([['01', 'Explore', 'Browse services and choose the provider that fits your needs.'], ['02', 'Select a time', 'View real-time availability and select a convenient appointment.'], ['03', 'Confirm', 'Submit your booking and manage it from your personal dashboard.']] as [$number, $title, $description])
                            <div class="relative">
                                <span class="text-6xl font-extrabold text-indigo-100">
                                    {{ $number }}
                                </span>

                                <h3 class="-mt-3 text-2xl font-bold text-slate-900">
                                    {{ $title }}
                                </h3>

                                <p class="mt-4 leading-7 text-slate-600">
                                    {{ $description }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Provider CTA --}}
            <section id="for-businesses" class="mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">
                <div
                    class="relative overflow-hidden rounded-[2.5rem] border border-indigo-300/20 bg-gradient-to-br from-indigo-600 to-slate-900 px-7 py-14 shadow-2xl shadow-indigo-950/50 sm:px-12 lg:px-16 lg:py-20">
                    <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl"></div>

                    <div class="absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-violet-400/20 blur-3xl"></div>

                    <div class="relative grid items-center gap-10 lg:grid-cols-[1fr_auto]">
                        <div class="max-w-3xl">
                            <p class="text-sm font-bold uppercase tracking-[0.24em] text-cyan-200">
                                Built for service providers
                            </p>

                            <h2 class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                                Turn availability into opportunity.
                            </h2>

                            <p class="mt-5 text-lg leading-8 text-indigo-100">
                                Organize services, publish appointment slots and
                                manage every customer booking through one
                                professional platform.
                            </p>
                        </div>

                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="inline-flex items-center justify-center rounded-2xl bg-white px-7 py-4 font-bold text-slate-950 shadow-xl transition hover:-translate-y-0.5 hover:bg-cyan-50">
                                Go to dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center justify-center rounded-2xl bg-white px-7 py-4 font-bold text-slate-950 shadow-xl transition hover:-translate-y-0.5 hover:bg-cyan-50">
                                    Create an account
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="relative z-10 border-t border-slate-200 bg-slate-50">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-6 px-6 py-10 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                        </svg>
                    </span>

                    <span class="font-bold text-slate-900">
                        BookEase
                    </span>
                </a>

                <p class="text-sm text-slate-500">
                    &copy; {{ now()->year }} BookEase.
                    Service booking made simple.
                </p>

                <div class="flex items-center gap-5 text-sm text-slate-500">
                    <a href="#features" class="transition hover:text-indigo-600">
                        Features
                    </a>

                    <a href="#how-it-works" class="transition hover:text-indigo-600">
                        How it works
                    </a>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>