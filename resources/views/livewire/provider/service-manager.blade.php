<div class="min-h-[calc(100vh-4rem)] bg-slate-50">
    <x-page-header title="Manage your services."
        subtitle="Create your offerings, manage pricing and availability, and keep everything customers see accurate and up to date.">
        @if ($hasBusiness && ! $showForm)
            <x-slot:actions>
                <button type="button" wire:click="startCreate"
                    class="inline-flex w-fit items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3.5 text-sm font-bold text-white shadow-xl transition hover:-translate-y-0.5 hover:bg-indigo-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                    </svg>
                    Add new service
                </button>
            </x-slot:actions>
        @endif
    </x-page-header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        @if (session('success'))
            <div role="alert" class="mb-7 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @error('delete')
            <div role="alert" class="mb-7 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800">
                {{ $message }}
            </div>
        @enderror

        @if (! $hasBusiness)
            <section class="rounded-xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 10h6M9 14h6M9 18h6" />
                    </svg>
                </span>
                <h2 class="mt-5 text-2xl font-extrabold text-slate-950">Create your business profile first</h2>
                <p class="mx-auto mt-2 max-w-xl text-slate-600">Every service must belong to a business. Add your business information before creating your first offering.</p>
                <a href="{{ route('provider.business.profile') }}"
                    class="mt-6 inline-flex rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-6 py-3 font-bold text-white shadow-lg shadow-indigo-500/20">
                    Create business profile
                </a>
            </section>
        @else
            {{-- Create/edit form --}}
            @if ($showForm)
                <form wire:submit="save" class="mb-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg shadow-slate-950/5">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 bg-slate-50 px-6 py-5 sm:px-8">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Service editor</p>
                            <h2 class="mt-1 text-2xl font-extrabold text-slate-950">{{ $serviceId ? 'Edit service' : 'Create a service' }}</h2>
                        </div>
                        <button type="button" wire:click="cancelForm"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:text-slate-900" aria-label="Close form">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
                        </button>
                    </div>

                    <div class="space-y-6 p-6 sm:p-8">
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700">Service name <span class="text-rose-600">*</span></label>
                            <input id="name" type="text" wire:model.blur="name" placeholder="For example: Private tutoring"
                                class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                            @error('name') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-bold text-slate-700">Description</label>
                            <textarea id="description" rows="5" wire:model.blur="description" placeholder="Describe what customers receive and what is included"
                                class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"></textarea>
                            @error('description') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="durationMinutes" class="block text-sm font-bold text-slate-700">Duration in minutes <span class="text-rose-600">*</span></label>
                                <input id="durationMinutes" type="number" min="15" max="1440" step="15" wire:model.blur="durationMinutes"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                                @error('durationMinutes') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="price" class="block text-sm font-bold text-slate-700">Price in LKR <span class="text-rose-600">*</span></label>
                                <input id="price" type="number" min="0" step="0.01" wire:model.blur="price"
                                    class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                                @error('price') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <input type="checkbox" wire:model="isActive" class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span><span class="block text-sm font-bold text-slate-800">Active service</span><span class="mt-1 block text-sm text-slate-500">Active services are visible in the customer catalogue.</span></span>
                        </label>
                        @error('isActive') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-5 sm:flex-row sm:px-8">
                        <button type="button" wire:click="cancelForm" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                            class="rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">{{ $serviceId ? 'Update service' : 'Create service' }}</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>
            @endif

            {{-- Search and heading --}}
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
                    <div class="w-full max-w-2xl">
                        <label for="search" class="block text-sm font-bold text-slate-700">Search your services</label>
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                            </span>
                            <input id="search" type="search" wire:model.live.debounce.400ms="search" placeholder="Search by name or description"
                                class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 pl-12 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                        </div>
                    </div>

                    @if (! $showForm)
                        <button type="button" wire:click="startCreate" class="inline-flex w-fit items-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-bold text-white hover:bg-indigo-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" /></svg>
                            Add service
                        </button>
                    @endif
                </div>
            </section>

            <div wire:loading class="mt-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700">Updating services...</div>

            <div class="mt-8 flex items-end justify-between gap-4">
                <div><p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">Your catalogue</p><h2 class="mt-2 text-2xl font-extrabold text-slate-950">Business services</h2></div>
                <p class="text-sm font-medium text-slate-500">{{ $services->total() }} {{ \Illuminate\Support\Str::plural('service', $services->total()) }}</p>
            </div>

            {{-- Service cards --}}
            <div class="mt-5 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($services as $service)
                    <article wire:key="service-{{ $service->id }}" class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-950/10">
                        <div class="relative flex h-32 items-center justify-center bg-gradient-to-br from-indigo-700 via-indigo-600 to-cyan-500">
                            <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-2xl font-extrabold text-indigo-600">{{ strtoupper(substr($service->name, 0, 1)) }}</span>
                            <span class="absolute right-4 top-4 inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold {{ $service->is_active ? 'text-emerald-700' : 'text-slate-600' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $service->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>{{ $service->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <h3 class="text-xl font-extrabold text-slate-950">{{ $service->name }}</h3>
                            <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($service->description ?? 'No description provided.', 130) }}</p>

                            <dl class="mt-5 grid grid-cols-2 gap-px overflow-hidden rounded-2xl bg-slate-200 text-sm">
                                <div class="bg-slate-50 p-3"><dt class="text-xs text-slate-500">Duration</dt><dd class="mt-1 font-bold text-slate-900">{{ $service->duration_minutes }} min</dd></div>
                                <div class="bg-slate-50 p-3"><dt class="text-xs text-slate-500">Price</dt><dd class="mt-1 font-bold text-slate-900">Rs. {{ number_format((float) $service->price, 2) }}</dd></div>
                                <div class="bg-slate-50 p-3"><dt class="text-xs text-slate-500">Slots</dt><dd class="mt-1 font-bold text-slate-900">{{ $service->availability_slots_count }}</dd></div>
                                <div class="bg-slate-50 p-3"><dt class="text-xs text-slate-500">Bookings</dt><dd class="mt-1 font-bold text-slate-900">{{ $service->bookings_count }}</dd></div>
                            </dl>

                            <div class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-5">
                                <button type="button" wire:click="edit({{ $service->id }})" class="rounded-xl border border-indigo-200 px-3 py-2.5 text-xs font-bold text-indigo-700 hover:bg-indigo-50">Edit</button>
                                <button type="button" wire:click="toggleActive({{ $service->id }})" wire:loading.attr="disabled" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-50">{{ $service->is_active ? 'Deactivate' : 'Activate' }}</button>
                                <button type="button" wire:click="delete({{ $service->id }})" wire:confirm="Are you sure you want to delete this service?" wire:loading.attr="disabled" class="rounded-xl border border-rose-200 px-3 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 disabled:opacity-50">Delete</button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm md:col-span-2 xl:col-span-3">
                        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600"><svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" /></svg></span>
                        <h2 class="mt-5 text-xl font-extrabold text-slate-950">No services found</h2>
                        <p class="mt-2 text-slate-600">Create your first service or change the search term.</p>
                        @if ($search === '')
                            <button type="button" wire:click="startCreate" class="mt-6 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700">Create first service</button>
                        @endif
                    </div>
                @endforelse
            </div>

            <div class="mt-10">{{ $services->links() }}</div>
        @endif
    </main>
</div>