<x-guest-layout>
    <div class="relative min-h-screen overflow-hidden bg-white">
        <div class="relative z-10 flex min-h-screen">
            {{-- Left information panel --}}
            <section
                class="relative hidden w-[46%] overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-indigo-800 px-12 py-10 text-white lg:flex lg:flex-col lg:justify-between xl:px-16">
                <div aria-hidden="true"
                    class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-cyan-400/20 blur-3xl">
                </div>

                {{-- Brand --}}
                <a href="{{ url('/') }}" class="relative flex w-fit items-center gap-3">
                    <span
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 shadow-lg shadow-cyan-950/30">
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

                        <span class="block text-[10px] font-semibold uppercase tracking-[0.24em] text-indigo-200">
                            Booking simplified
                        </span>
                    </span>
                </a>

                {{-- Main message --}}
                <div class="relative max-w-xl py-12">
                    <x-page-header variant="marketing" bare eyebrow="Secure account recovery">
                        <x-slot:title>
                            <span class="text-5xl font-extrabold leading-tight tracking-[-0.04em] xl:text-6xl">
                                Let’s get you
                                <span class="bg-gradient-to-r from-cyan-300 to-indigo-300 bg-clip-text text-transparent">
                                    back on track.
                                </span>
                            </span>
                        </x-slot:title>

                        <x-slot:subtitle>
                            Enter the email address connected to your BookEase
                            account. We’ll send you a secure link for choosing a
                            new password.
                        </x-slot:subtitle>
                    </x-page-header>

                    <div class="mt-10 space-y-4">
                        <div class="flex items-start gap-4">
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-300/15 text-cyan-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z" />

                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6" />
                                </svg>
                            </span>

                            <div>
                                <p class="font-bold text-white">
                                    Check your inbox
                                </p>

                                <p class="mt-1 text-sm leading-6 text-indigo-200">
                                    The recovery link will be delivered to your
                                    registered email address.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-300/15 text-indigo-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3 5 6v5c0 4.5 2.8 8.5 7 10 4.2-1.5 7-5.5 7-10V6l-7-3Z" />

                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" />
                                </svg>
                            </span>

                            <div>
                                <p class="font-bold text-white">
                                    Reset securely
                                </p>

                                <p class="mt-1 text-sm leading-6 text-indigo-200">
                                    Your existing password remains protected
                                    throughout the recovery process.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="relative text-sm text-indigo-200">
                    &copy; {{ now()->year }} BookEase.
                    Service booking made simple.
                </p>
            </section>

            {{-- Password recovery area --}}
            <main class="flex w-full items-center justify-center px-6 py-10 sm:px-10 lg:w-[54%] lg:px-16">
                <div class="w-full max-w-md">
                    {{-- Mobile brand --}}
                    <a href="{{ url('/') }}" class="mb-12 flex w-fit items-center gap-3 lg:hidden">
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 shadow-lg shadow-indigo-500/20">
                            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />

                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.5 15 2 2 5-5" />
                            </svg>
                        </span>

                        <span class="text-xl font-extrabold tracking-tight text-slate-950">
                            BookEase
                        </span>
                    </a>

                    <div
                        class="mb-7 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 10V8a5 5 0 0 1 9.8-1.4" />

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 14v-1a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1" />

                            <path stroke-linecap="round" stroke-linejoin="round" d="m18 9 3-3-3-3M21 6h-7" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-indigo-600">
                            Password recovery
                        </p>

                        <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-950">
                            Forgot your password?
                        </h2>

                        <p class="mt-4 leading-7 text-slate-600">
                            No problem. Enter your registered email address and
                            we’ll send you a password-reset link.
                        </p>
                    </div>

                    @session('status')
                        <div
                            class="mt-7 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                            </svg>

                            {{ $value }}
                        </div>
                    @endsession

                    <x-validation-errors
                        class="mt-7 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700" />

                    <form method="POST" action="{{ route('password.email') }}" class="mt-8">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700">
                                Email address
                            </label>

                            <div class="relative mt-2">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z" />

                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6" />
                                    </svg>
                                </span>

                                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="username" placeholder="you@example.com"
                                    class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                            </div>
                        </div>

                        <button type="submit"
                            class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-6 py-3.5 text-base font-bold text-white shadow-xl shadow-indigo-500/20 transition hover:-translate-y-0.5 hover:from-indigo-700 hover:to-cyan-600 hover:shadow-2xl hover:shadow-indigo-500/25 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Email password reset link

                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        </button>
                    </form>

                    <div class="mt-8 border-t border-slate-200 pt-7 text-center">
                        <p class="text-sm text-slate-600">
                            Remembered your password?

                            <a href="{{ route('login') }}"
                                class="ml-1 font-bold text-indigo-600 transition hover:text-indigo-800">
                                Return to login
                            </a>
                        </p>
                    </div>

                    <div class="mt-7 text-center">
                        <a href="{{ url('/') }}"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-indigo-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                            </svg>

                            Return to homepage
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-guest-layout>