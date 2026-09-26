<div class="min-h-screen bg-slate-50 pb-16">
    <x-page-header eyebrow="Provider workspace" title="Availability"
        subtitle="Build a bookable schedule for your services and keep every appointment slot under control.">
        @if ($hasBusiness && $services->isNotEmpty() && ! $showForm)
            <x-slot:actions>
                <button type="button" wire:click="startCreate"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5 hover:bg-indigo-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add time slot
                </button>
            </x-slot:actions>
        @endif
    </x-page-header>

    <main class="relative z-10 mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        @if (session('success'))
            <div role="alert" class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                {{ session('success') }}
            </div>
        @endif

        @foreach (['form', 'delete'] as $errorBag)
            @error($errorBag)
                <div role="alert" class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 shadow-sm">{{ $message }}</div>
            @enderror
        @endforeach

        @if (! $hasBusiness)
            <section class="rounded-xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                </div>
                <h2 class="mt-5 text-2xl font-bold text-slate-900">Create your business profile first</h2>
                <p class="mx-auto mt-2 max-w-md text-slate-600">Your availability must be connected to a service offered by your business.</p>
                <a href="{{ route('provider.business.profile') }}" class="mt-7 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">Create business profile</a>
            </section>
        @elseif ($services->isEmpty())
            <section class="rounded-xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <h2 class="mt-5 text-2xl font-bold text-slate-900">Create a service first</h2>
                <p class="mx-auto mt-2 max-w-md text-slate-600">Every appointment slot needs a service, duration, and price before customers can book it.</p>
                <a href="{{ route('provider.services.index') }}" class="mt-7 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">Manage services</a>
            </section>
        @else
            <section class="mb-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Total slots</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $slots->total() }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Services offered</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $services->count() }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Schedule status</p>
                    <p class="mt-2 inline-flex items-center gap-2 text-base font-bold text-emerald-700"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Ready for bookings</p>
                </div>
            </section>

            @if ($showForm)
                <form wire:submit="save" class="mb-6 overflow-hidden rounded-3xl border border-indigo-100 bg-white shadow-xl shadow-indigo-100/60">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-indigo-50/70 px-6 py-5 sm:px-8">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Schedule editor</p>
                            <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $slotId ? 'Edit time slot' : 'Create a new time slot' }}</h2>
                        </div>
                        <button type="button" wire:click="cancelForm" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-500 hover:bg-white hover:text-slate-900">Close</button>
                    </div>

                    <div class="space-y-6 p-6 sm:p-8">
                        <div>
                            <label for="serviceId" class="text-sm font-semibold text-slate-700">Service <span class="text-rose-500">*</span></label>
                            <select id="serviceId" wire:model="serviceId" class="mt-2 block w-full rounded-xl border-slate-300 bg-white py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a service</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration_minutes }} minutes)</option>
                                @endforeach
                            </select>
                            @error('serviceId') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="startsAt" class="text-sm font-semibold text-slate-700">Start date and time <span class="text-rose-500">*</span></label>
                                <input id="startsAt" type="datetime-local" wire:model="startsAt" class="mt-2 block w-full rounded-xl border-slate-300 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('startsAt') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                                <p class="mt-2 text-xs leading-5 text-slate-500">The end time is calculated from the service duration.</p>
                            </div>
                            <div>
                                <label for="capacity" class="text-sm font-semibold text-slate-700">Customer capacity <span class="text-rose-500">*</span></label>
                                <input id="capacity" type="number" min="1" max="100" wire:model="capacity" class="mt-2 block w-full rounded-xl border-slate-300 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('capacity') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                                <p class="mt-2 text-xs leading-5 text-slate-500">Maximum customers allowed in this slot.</p>
                            </div>
                        </div>

                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <input type="checkbox" wire:model="isActive" class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span><span class="block text-sm font-bold text-slate-800">Make this slot active</span><span class="mt-1 block text-sm text-slate-500">Only active slots are visible and bookable by customers.</span></span>
                        </label>
                        @error('isActive') <p class="text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-5 sm:px-8">
                        <button type="button" wire:click="cancelForm" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">{{ $slotId ? 'Update slot' : 'Create slot' }}</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Your calendar</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Appointment slots</h2>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:w-[34rem]">
                        <div>
                            <label for="filterServiceId" class="text-xs font-bold uppercase tracking-wide text-slate-500">Service</label>
                            <select id="filterServiceId" wire:model.live="filterServiceId" class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All services</option>
                                @foreach ($services as $service)<option value="{{ $service->id }}">{{ $service->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label for="dateFilter" class="text-xs font-bold uppercase tracking-wide text-slate-500">Date</label>
                            <input id="dateFilter" type="date" wire:model.live="dateFilter" class="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </section>

            <div wire:loading class="mt-4 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">Updating availability...</div>

            <div class="mt-5 space-y-4">
                @forelse ($slots as $slot)
                    <article wire:key="slot-{{ $slot->id }}" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                        <div class="flex flex-col lg:flex-row">
                            <div class="flex w-full items-center gap-4 border-b border-slate-100 bg-slate-50 p-5 lg:w-64 lg:border-b-0 lg:border-r">
                                <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-md shadow-indigo-200">
                                    <span class="text-xs font-bold uppercase">{{ $slot->starts_at->format('M') }}</span>
                                    <span class="text-xl font-black leading-none">{{ $slot->starts_at->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ $slot->starts_at->format('l') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $slot->starts_at->format('Y') }}</p>
                                </div>
                            </div>

                            <div class="flex-1 p-5 sm:p-6">
                                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="truncate text-lg font-bold text-slate-900">{{ $slot->service->name }}</h3>
                                            @if ($slot->ends_at->isPast())
                                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Past</span>
                                            @elseif ($slot->is_active)
                                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Active</span>
                                            @else
                                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Inactive</span>
                                            @endif
                                        </div>
                                        <div class="mt-4 flex flex-wrap gap-x-6 gap-y-3 text-sm">
                                            <span class="inline-flex items-center gap-2 font-semibold text-slate-700"><svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>{{ $slot->starts_at->format('h:i A') }} – {{ $slot->ends_at->format('h:i A') }}</span>
                                            <span class="text-slate-500">Capacity <strong class="text-slate-800">{{ $slot->capacity }}</strong></span>
                                            <span class="text-slate-500">Active bookings <strong class="text-slate-800">{{ $slot->active_bookings_count }}/{{ $slot->capacity }}</strong></span>
                                            <span class="text-slate-500">All bookings <strong class="text-slate-800">{{ $slot->bookings_count }}</strong></span>
                                        </div>
                                    </div>

                                    <div class="flex shrink-0 flex-wrap gap-2">
                                        @if (! $slot->ends_at->isPast())
                                            <button type="button" wire:click="edit({{ $slot->id }})" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700 hover:bg-indigo-100">Edit</button>
                                            <button type="button" wire:click="toggleActive({{ $slot->id }})" wire:loading.attr="disabled" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-50">{{ $slot->is_active ? 'Deactivate' : 'Activate' }}</button>
                                        @endif
                                        <button type="button" wire:click="delete({{ $slot->id }})" wire:confirm="Are you sure you want to delete this time slot?" wire:loading.attr="disabled" class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-bold text-rose-700 hover:bg-rose-50 disabled:opacity-50">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500"><svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 18.75V7.5A2.25 2.25 0 0 1 6 5.25h12a2.25 2.25 0 0 1 2.25 2.25v11.25m-16.5 0A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25m-16.5 0v-7.5h16.5v7.5" /></svg></div>
                        <h2 class="mt-4 text-lg font-bold text-slate-900">No time slots found</h2>
                        <p class="mt-2 text-sm text-slate-500">Create your first appointment slot or adjust the filters above.</p>
                        @if (! $showForm)<button type="button" wire:click="startCreate" class="mt-5 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700">Add time slot</button>@endif
                    </div>
                @endforelse
            </div>

            @if ($slots->hasPages())
                <div class="mt-8">{{ $slots->links() }}</div>
            @endif
        @endif
    </main>
</div>