@props([
    'eyebrow' => null,
    'title' => '',
    'subtitle' => null,
    'align' => 'left',
    'action' => null,
    'class' => '',
])

@php
    $wrapperClasses = 'mb-10 md:mb-14 flex flex-col gap-6';
    if ($align === 'center') {
        $wrapperClasses .= ' items-center text-center';
    }
    if ($action) {
        $wrapperClasses .= ' md:flex-row md:items-end md:justify-between md:gap-12';
    }
@endphp

<div class="{{ $wrapperClasses }} {{ $class }}">
    <div class="{{ $align === 'center' ? 'max-w-2xl mx-auto' : 'max-w-2xl' }}">
        @if($eyebrow)
            <p class="text-eyebrow mb-3">{{ $eyebrow }}</p>
        @endif
        <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-foreground text-balance">
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="mt-4 text-base md:text-lg text-muted-foreground leading-relaxed text-pretty">
                {{ $subtitle }}
            </p>
        @endif
    </div>
    @if($action)
        <div class="shrink-0">{!! $action !!}</div>
    @endif
</div>
