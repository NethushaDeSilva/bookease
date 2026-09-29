@props([
    'variant' => 'app',
    'bare' => false,
    'title' => null,
    'subtitle' => null,
    'back' => null,
])

@php
    $isMarketing = $variant === 'marketing';

    $titleClasses = $isMarketing
        ? 'text-3xl text-white sm:text-4xl'
        : 'text-2xl text-slate-950 sm:text-3xl';

    $subtitleClasses = $isMarketing ? 'text-indigo-100' : 'text-slate-600';

    $backClasses = $isMarketing
        ? 'text-indigo-200 hover:text-white'
        : 'text-indigo-600 hover:text-indigo-800';
@endphp

@if (! $bare)
<section {{ $attributes->class(['relative overflow-hidden', 'border-b border-slate-200 bg-slate-50' => ! $isMarketing, 'bg-gradient-to-br from-slate-950 via-indigo-950 to-indigo-900 text-white' => $isMarketing]) }}>
    @if ($isMarketing)
        <div aria-hidden="true" class="pointer-events-none absolute -right-24 -top-32 h-96 w-96 rounded-full bg-cyan-400/20 blur-3xl"></div>
    @endif

    <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
@endif

    @if ($back)
        <a href="{{ $back['href'] }}" class="inline-flex items-center gap-2 text-sm font-bold transition {{ $backClasses }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
            </svg>
            {{ $back['label'] }}
        </a>
    @endif

    <div class="flex flex-col gap-6 {{ $back ? 'mt-6' : '' }} sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl">
            <h1 class="font-bold tracking-tight {{ $titleClasses }}">
                {{ $title }}
            </h1>

            @if ($subtitle)
                <p class="mt-3 max-w-xl leading-7 {{ $subtitleClasses }}">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        @isset($actions)
            <div class="flex shrink-0 flex-wrap items-center gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>

@if (! $bare)
    </div>
</section>
@endif
