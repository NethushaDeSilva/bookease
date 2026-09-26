<div class="min-h-[calc(100vh-4rem)] bg-slate-50">
    <x-page-header :back="['href' => route('customer.bookings.index'), 'label' => 'Back to my bookings']"
        eyebrow="★ Completed appointment" title="Share your experience."
        subtitle="Your honest feedback helps other customers choose confidently and helps service providers improve." />

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        <div class="grid items-start gap-6 lg:grid-cols-[20rem_1fr]">
            {{-- Appointment summary --}}
            <aside class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm lg:sticky lg:top-24">
                <div
                    class="relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-cyan-500 p-6 text-white">
                    <div aria-hidden="true"
                        class="absolute -right-10 -top-12 h-36 w-36 rounded-full border-[22px] border-white/10"></div>

                    <span
                        class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-xl font-extrabold text-indigo-600 shadow-lg">
                        {{ strtoupper(substr($booking->service->name, 0, 1)) }}
                    </span>

                    <p class="relative mt-5 text-xs font-bold uppercase tracking-[0.16em] text-indigo-100">
                        {{ $booking->service->business->name }}
                    </p>

                    <h2 class="relative mt-2 text-2xl font-extrabold">
                        {{ $booking->service->name }}
                    </h2>
                </div>

                <dl class="divide-y divide-slate-100 p-6">
                    <div class="pb-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Booking reference</dt>
                        <dd class="mt-1.5 font-bold text-slate-900">{{ $booking->booking_reference }}</dd>
                    </div>

                    <div class="py-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Appointment</dt>
                        <dd class="mt-1.5 font-bold text-slate-900">
                            {{ $booking->slot->starts_at->format('D, d M Y') }}
                        </dd>
                        <dd class="mt-1 text-sm text-slate-600">
                            {{ $booking->slot->starts_at->format('h:i A') }}
                            –
                            {{ $booking->slot->ends_at->format('h:i A') }}
                        </dd>
                    </div>

                    <div class="pt-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Price paid</dt>
                        <dd class="mt-1.5 text-xl font-extrabold text-slate-950">
                            Rs. {{ number_format((float) $booking->price, 2) }}
                        </dd>
                    </div>
                </dl>
            </aside>

            {{-- Review form --}}
            <form wire:submit="createReview" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">Your feedback</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-slate-950">How was your appointment?</h2>
                    <p class="mt-2 text-slate-600">Select a rating and optionally tell others about your experience.</p>
                </div>

                <div class="mt-8">
                    <label class="block text-sm font-bold text-slate-700">
                        Your rating <span class="text-rose-600">*</span>
                    </label>

                    <div class="mt-3 flex flex-wrap gap-2" role="radiogroup" aria-label="Choose a rating">
                        @for ($star = 1; $star <= 5; $star++)
                                            <button type="button" wire:click="$set('rating', {{ $star }})" role="radio"
                                                aria-label="{{ $star }} star rating"
                                                aria-checked="{{ $rating === $star ? 'true' : 'false' }}"
                                                class="flex h-14 w-14 items-center justify-center rounded-2xl border text-3xl leading-none shadow-sm transition hover:-translate-y-0.5
                                                        {{ $rating >= $star
                            ? 'border-amber-300 bg-amber-50 text-amber-400 shadow-amber-100'
                            : 'border-slate-200 bg-white text-slate-300 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-300' }}">
                                                ★
                                            </button>
                        @endfor
                    </div>

                    <div class="mt-4 min-h-6">
                        @if ($rating > 0)
                            @php
                                $ratingText = match ($rating) {
                                    1 => 'Very disappointing',
                                    2 => 'Could be better',
                                    3 => 'Good experience',
                                    4 => 'Very good experience',
                                    5 => 'Excellent experience',
                                };
                            @endphp

                            <p
                                class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-sm font-bold text-amber-700">
                                {{ $rating }}/5 — {{ $ratingText }}
                            </p>
                        @endif
                    </div>

                    @error('rating')
                        <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-7">
                    <div class="flex items-center justify-between gap-4">
                        <label for="review-comment" class="block text-sm font-bold text-slate-700">
                            Your comments <span class="font-medium text-slate-400">(optional)</span>
                        </label>

                        <span class="text-xs font-medium text-slate-400">Maximum 2,000 characters</span>
                    </div>

                    <textarea id="review-comment" rows="7" maxlength="2000" wire:model.blur="comment"
                        placeholder="What did you like? What could have been better? Share useful details about your experience."
                        class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50 p-4 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"></textarea>

                    @error('comment')
                        <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div
                    class="mt-6 flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path stroke-linecap="round" d="M12 11v5M12 8h.01"></path>
                        </svg>
                    </span>

                    <p class="leading-6">
                        Your rating and comments will be publicly visible to customers browsing this service. Please
                        keep your review honest and respectful.
                    </p>
                </div>

                <div class="mt-8 flex flex-col-reverse justify-end gap-3 border-t border-slate-100 pt-6 sm:flex-row">
                    <a href="{{ route('customer.bookings.index') }}"
                        class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        Cancel
                    </a>

                    <button type="submit" wire:loading.attr="disabled" wire:target="createReview"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 transition hover:-translate-y-0.5 hover:from-indigo-700 hover:to-cyan-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <span wire:loading.remove wire:target="createReview">Submit review</span>
                        <span wire:loading wire:target="createReview">Submitting...</span>

                        <svg wire:loading.remove wire:target="createReview" class="h-4 w-4" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>