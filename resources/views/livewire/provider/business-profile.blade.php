<div class="min-h-screen bg-slate-50 pb-16">
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-indigo-800">
        <div class="absolute inset-0 opacity-30"
            style="background-image: radial-gradient(circle at 18% 18%, rgba(34,211,238,.35), transparent 28%), radial-gradient(circle at 85% 10%, rgba(129,140,248,.4), transparent 24%);">
        </div>
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <div class="max-w-2xl">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-200">
                    <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                    Provider workspace
                </div>
                <h1 class="mt-5 text-3xl font-bold tracking-tight text-white sm:text-4xl">My Business</h1>
                <p class="mt-3 max-w-xl text-base leading-7 text-indigo-100">
                    Keep your public business information accurate, professional, and ready for customers.
                </p>
            </div>
        </div>
    </section>

    <main class="relative z-10 mx-auto -mt-5 max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="alert"
                class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[20rem_minmax(0,1fr)]">
            <aside class="space-y-5">
                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 p-6 text-white">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </div>
                        <p class="mt-5 text-xs font-bold uppercase tracking-[0.18em] text-indigo-200">Business profile
                        </p>
                        <h2 class="mt-2 break-words text-xl font-bold">{{ $name !== '' ? $name : 'Your business' }}</h2>
                    </div>

                    <div class="p-6">
                        @if ($businessId)
                            @php
                                $statusClass = match ($businessStatus) {
                                    'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                    'suspended' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                    default => 'bg-amber-50 text-amber-700 ring-amber-200',
                                };
                                $statusDot = match ($businessStatus) {
                                    'active' => 'bg-emerald-500',
                                    'suspended' => 'bg-rose-500',
                                    default => 'bg-amber-500',
                                };
                            @endphp
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Current status</p>
                            <span
                                class="mt-3 inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-bold ring-1 ring-inset {{ $statusClass }}">
                                <span class="h-2 w-2 rounded-full {{ $statusDot }}"></span>
                                {{ ucfirst($businessStatus) }}
                            </span>
                            <p class="mt-4 text-sm leading-6 text-slate-500">
                                {{ $businessStatus === 'active' ? 'Your business is visible to customers and can receive bookings.' : 'Only active businesses are displayed to customers.' }}
                            </p>
                        @else
                            <div class="flex items-start gap-3 rounded-2xl bg-amber-50 p-4 text-amber-800">
                                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                <p class="text-sm leading-6">Your new profile will be submitted for administrator approval.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="font-bold text-slate-900">Profile tips</h3>
                    <ul class="mt-4 space-y-4 text-sm leading-6 text-slate-600">
                        <li class="flex gap-3"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-indigo-500"></span>Use
                            the name customers recognize.</li>
                        <li class="flex gap-3"><span
                                class="mt-2 h-2 w-2 shrink-0 rounded-full bg-cyan-500"></span>Explain your services
                            clearly.</li>
                        <li class="flex gap-3"><span
                                class="mt-2 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>Keep contact details up
                            to date.</li>
                    </ul>
                </section>
            </aside>

            <form wire:submit="save" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-6 sm:px-8">
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Public information</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">Business details</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">This information helps customers understand and
                        contact your business.</p>
                </div>

                <div class="space-y-8 p-6 sm:p-8">
                    <section>
                        <div class="mb-5 flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Business identity</h3>
                                <p class="text-sm text-slate-500">The main details shown in your listing.</p>
                            </div>
                        </div>

                        <div>
                            <label for="name" class="text-sm font-semibold text-slate-700">Business name <span
                                    class="text-rose-500">*</span></label>
                            <input id="name" type="text" wire:model.blur="name" autocomplete="organization"
                                placeholder="Enter your business name"
                                class="mt-2 block w-full rounded-xl border-slate-300 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between gap-3">
                                <label for="description"
                                    class="text-sm font-semibold text-slate-700">Description</label>
                                <span class="text-xs text-slate-400">Maximum 2,000 characters</span>
                            </div>
                            <textarea id="description" rows="6" wire:model.blur="description"
                                placeholder="Tell customers what makes your business special and describe the services you provide..."
                                class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('description')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </section>

                    <div class="border-t border-slate-100"></div>

                    <section>
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102A1.125 1.125 0 0 0 5.872 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Contact information</h3>
                                <p class="text-sm text-slate-500">Give customers a reliable way to reach you.</p>
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="email" class="text-sm font-semibold text-slate-700">Business email</label>
                                <input id="email" type="email" wire:model.blur="email" autocomplete="email"
                                    placeholder="hello@business.com"
                                    class="mt-2 block w-full rounded-xl border-slate-300 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('email')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="text-sm font-semibold text-slate-700">Phone number</label>
                                <input id="phone" type="tel" wire:model.blur="phone" autocomplete="tel"
                                    placeholder="+94 77 123 4567"
                                    class="mt-2 block w-full rounded-xl border-slate-300 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('phone')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="address" class="text-sm font-semibold text-slate-700">Business address</label>
                            <input id="address" type="text" wire:model.blur="address" autocomplete="street-address"
                                placeholder="Street, city and postal code"
                                class="mt-2 block w-full rounded-xl border-slate-300 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('address')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </section>
                </div>

                <div
                    class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                    <p class="text-xs leading-5 text-slate-500">Changes appear in your public profile after saving.</p>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span wire:loading.remove
                            wire:target="save">{{ $businessId ? 'Update business' : 'Create business' }}</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>