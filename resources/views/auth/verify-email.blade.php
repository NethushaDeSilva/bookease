<x-guest-layout>
    <div class="relative min-h-screen overflow-hidden bg-white">
        <div class="relative z-10 flex min-h-screen">
            <section class="relative hidden w-[46%] overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-indigo-800 px-12 py-10 text-white lg:flex lg:flex-col lg:justify-between xl:px-16">
                <div aria-hidden="true" class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-cyan-400/20 blur-3xl"></div>

                <a href="{{ url('/') }}" class="relative flex w-fit items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 shadow-lg shadow-cyan-950/30">
                        <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.5 15 2 2 5-5" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-xl font-extrabold tracking-tight">BookEase</span>
                    </span>
                </a>

                <div class="relative max-w-xl py-12">
                    <x-page-header variant="marketing" bare>
                        <x-slot:title>
                            <span class="text-5xl font-extrabold leading-tight tracking-[-0.04em] xl:text-6xl">
                                Your BookEase account is almost
                                <span class="bg-gradient-to-r from-cyan-300 to-indigo-300 bg-clip-text text-transparent">ready.</span>
                            </span>
                        </x-slot:title>

                        <x-slot:subtitle>
                            Confirming your email helps keep your account secure and ensures you receive important booking updates.
                        </x-slot:subtitle>
                    </x-page-header>
                    <div class="mt-10 rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                        <div class="flex items-start gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-300/15 text-cyan-200">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-bold">Check your inbox</p>
                                <p class="mt-1 text-sm leading-6 text-indigo-200">Open the verification message from BookEase and select its secure confirmation link.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="relative text-sm text-indigo-200">&copy; {{ now()->year }} BookEase. Service booking made simple.</p>
            </section>

            <main class="flex w-full items-center justify-center px-6 py-10 sm:px-10 lg:w-[54%] lg:px-16">
                <div class="w-full max-w-md">
                    <a href="{{ url('/') }}" class="mb-12 flex w-fit items-center gap-3 lg:hidden">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 shadow-lg shadow-indigo-500/20">
                            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.5 15 2 2 5-5" />
                            </svg>
                        </span>
                        <span class="text-xl font-extrabold tracking-tight text-slate-950">BookEase</span>
                    </a>

                    <div>
                        <h1 class="text-4xl font-extrabold tracking-tight text-slate-950">Check your inbox</h1>
                        <p class="mt-3 leading-7 text-slate-600">We sent a verification link to <span class="font-semibold text-slate-800">{{ auth()->user()->email }}</span>. Select the link in that email to activate your account.</p>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-7 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium leading-6 text-emerald-800">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                            A new verification link has been sent. Please check your inbox and spam folder.
                        </div>
                    @endif

                    <div class="mt-8 rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="font-bold text-slate-900">Didn’t receive the email?</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-600">It can take a moment to arrive. Check your spam folder, then request a fresh link if needed.</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-6 py-3.5 text-base font-bold text-white shadow-xl shadow-indigo-500/20 transition hover:-translate-y-0.5 hover:from-indigo-700 hover:to-cyan-600 hover:shadow-2xl hover:shadow-indigo-500/25 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Resend verification email
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg>
                            </button>
                        </form>
                    </div>

                    <div class="mt-8 flex flex-col gap-4 border-t border-slate-200 pt-7 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-2 font-semibold text-indigo-600 transition hover:text-indigo-800">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" /></svg>
                            Use a different email
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 font-semibold text-slate-500 transition hover:text-rose-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 5V4a1 1 0 0 1 1-1h13a1 1 0 0 1 1 1v3" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 16v4a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-1" /></svg>
                                Log out
                            </button>
                        </form>
                    </div>

                    <div class="mt-8 text-center">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-indigo-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                            Return to homepage
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-guest-layout>