<div class="py-10">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        @if ($bookingCreated)
            {{-- Successful booking --}}
            <div class="rounded-lg bg-white p-8 text-center shadow">
                <div
                    class="mx-auto flex h-16 w-16 items-center
                           justify-center rounded-full bg-green-100"
                >
                    <svg
                        class="h-9 w-9 text-green-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4.5 12.75 10.5 18.75 19.5 5.25"
                        />
                    </svg>
                </div>

                <h1 class="mt-5 text-3xl font-bold text-gray-900">
                    Booking submitted
                </h1>

                <p class="mt-3 text-gray-600">
                    Your booking was created successfully and is waiting
                    for confirmation from the service provider.
                </p>

                <div class="mt-6 rounded-lg bg-gray-50 p-5">
                    <p class="text-sm text-gray-500">
                        Booking reference
                    </p>

                    <p class="mt-1 text-xl font-bold text-indigo-700">
                        {{ $createdBookingReference }}
                    </p>

                    <p class="mt-2 text-sm text-gray-500">
                        Keep this reference for future enquiries.
                    </p>
                </div>

                <div
                    class="mt-8 flex flex-col justify-center gap-3
                           sm:flex-row"
                >
                    <a
                        href="{{ route(
                            'customer.services.show',
                            $service
                        ) }}"
                        class="rounded-md border border-gray-300 bg-white
                               px-5 py-2.5 text-sm font-semibold
                               text-gray-700 hover:bg-gray-50"
                    >
                        Return to service
                    </a>

                    <a
                        href="{{ route('customer.services.index') }}"
                        class="rounded-md bg-indigo-600 px-5 py-2.5
                               text-sm font-semibold text-white
                               hover:bg-indigo-700"
                    >
                        Browse more services
                    </a>
                </div>
            </div>
        @else
            @php
                $remainingCapacity = max(
                    0,
                    $slot->capacity - $slot->active_bookings_count
                );

                $isStillAvailable = $slot->is_active
                    && $slot->starts_at->isFuture()
                    && $remainingCapacity > 0;
            @endphp

            <a
                href="{{ route(
                    'customer.services.show',
                    $service
                ) }}"
                class="inline-flex text-sm font-semibold
                       text-indigo-600 hover:text-indigo-800"
            >
                ← Back to service
            </a>

            <div class="mt-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    Confirm your booking
                </h1>

                <p class="mt-2 text-gray-600">
                    Review the appointment details before submitting.
                </p>
            </div>

            @error('slot')
                <div
                    class="mt-6 rounded-md border border-red-200
                           bg-red-50 p-4 text-red-800"
                    role="alert"
                >
                    {{ $message }}
                </div>
            @enderror

            {{-- Booking summary --}}
            <section class="mt-8 overflow-hidden rounded-lg bg-white shadow">
                <div class="bg-indigo-600 px-6 py-5">
                    <p class="text-sm font-semibold text-indigo-100">
                        {{ $service->business->name }}
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-white">
                        {{ $service->name }}
                    </h2>
                </div>

                <div class="p-6">
                    <dl class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-gray-500">
                                Appointment date
                            </dt>

                            <dd class="mt-1 font-semibold text-gray-900">
                                {{ $slot->starts_at->format(
                                    'l, d F Y'
                                ) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Appointment time
                            </dt>

                            <dd class="mt-1 font-semibold text-gray-900">
                                {{ $slot->starts_at->format('h:i A') }}
                                –
                                {{ $slot->ends_at->format('h:i A') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Duration
                            </dt>

                            <dd class="mt-1 font-semibold text-gray-900">
                                {{ $service->duration_minutes }} minutes
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Price
                            </dt>

                            <dd class="mt-1 text-xl font-bold text-gray-900">
                                Rs. {{ number_format(
                                    (float) $service->price,
                                    2
                                ) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Location
                            </dt>

                            <dd class="mt-1 font-semibold text-gray-900">
                                {{ $service->business->address
                                    ?? 'Contact the provider for the location' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Remaining places
                            </dt>

                            <dd class="mt-1 font-semibold text-gray-900">
                                {{ $remainingCapacity }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            @if ($isStillAvailable)
                <form
                    wire:submit="createBooking"
                    class="mt-6 rounded-lg bg-white p-6 shadow"
                >
                    <div>
                        <label
                            for="notes"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Notes for the service provider
                            <span class="font-normal text-gray-500">
                                (optional)
                            </span>
                        </label>

                        <textarea
                            id="notes"
                            rows="5"
                            maxlength="1000"
                            wire:model.blur="notes"
                            placeholder="Add any information the provider should know"
                            class="mt-1 block w-full rounded-md
                                   border-gray-300 shadow-sm
                                   focus:border-indigo-500
                                   focus:ring-indigo-500"
                        ></textarea>

                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-2 text-xs text-gray-500">
                            Maximum 1,000 characters.
                        </p>
                    </div>

                    <div
                        class="mt-6 rounded-md border border-blue-200
                               bg-blue-50 p-4 text-sm text-blue-800"
                    >
                        The booking will initially have a
                        <strong>Pending</strong> status. The provider must
                        confirm it before the appointment is finalized.
                    </div>

                    <div
                        class="mt-6 flex flex-col justify-end gap-3
                               border-t pt-6 sm:flex-row"
                    >
                        <a
                            href="{{ route(
                                'customer.services.show',
                                $service
                            ) }}"
                            class="rounded-md border border-gray-300
                                   bg-white px-5 py-2.5 text-center
                                   text-sm font-semibold text-gray-700
                                   hover:bg-gray-50"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="createBooking"
                            class="rounded-md bg-indigo-600 px-5 py-2.5
                                   text-sm font-semibold text-white
                                   hover:bg-indigo-700 disabled:cursor-not-allowed
                                   disabled:opacity-50"
                        >
                            <span
                                wire:loading.remove
                                wire:target="createBooking"
                            >
                                Confirm booking
                            </span>

                            <span
                                wire:loading
                                wire:target="createBooking"
                            >
                                Creating booking...
                            </span>
                        </button>
                    </div>
                </form>
            @else
                <div
                    class="mt-6 rounded-lg border border-red-200
                           bg-red-50 p-6 text-center"
                >
                    <h2 class="text-lg font-semibold text-red-800">
                        This appointment is no longer available
                    </h2>

                    <p class="mt-2 text-red-700">
                        Return to the service page and select another
                        appointment time.
                    </p>

                    <a
                        href="{{ route(
                            'customer.services.show',
                            $service
                        ) }}"
                        class="mt-5 inline-flex rounded-md bg-indigo-600
                               px-5 py-2.5 text-sm font-semibold
                               text-white hover:bg-indigo-700"
                    >
                        Choose another time
                    </a>
                </div>
            @endif
        @endif
    </div>
</div>