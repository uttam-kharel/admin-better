@props([
    'label' => null,
    'value' => null,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
    $interactive = $href ? 'group hover:shadow-card hover:-translate-y-0.5 transition-all' : '';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class("block rounded-xl bg-surface border border-border p-5 $interactive") }}>
    @isset($icon)
        <div class="size-10 rounded-lg bg-primary/10 text-primary grid place-items-center mb-4 [&>svg]:h-5 [&>svg]:w-5">
            {{ $icon }}
        </div>
    @endisset

    <p class="text-2xl font-bold tabular-nums">{{ $value }}{{ $slot }}</p>

    @if ($label)
        <p class="mt-1 text-sm text-muted-foreground">{{ $label }}</p>
    @endif
</{{ $tag }}>
