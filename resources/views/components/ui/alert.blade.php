@props([
    'variant' => 'info',
    'title' => null,
])

@php
    $variants = [
        'info'    => 'bg-primary-soft text-primary border-primary/20',
        'success' => 'bg-secondary-soft text-secondary border-secondary/20',
        'warning' => 'bg-emergency-soft text-emergency border-emergency/20',
        'error'   => 'bg-destructive/10 text-destructive border-destructive/20',
        'muted'   => 'bg-muted text-muted-foreground border-border',
    ];

    $classes = 'flex gap-3 rounded-xl border px-4 py-3 text-sm ' . ($variants[$variant] ?? $variants['info']);
@endphp

<div role="alert" {{ $attributes->class($classes) }}>
    @isset($icon)
        <span class="shrink-0 mt-0.5">{{ $icon }}</span>
    @endisset

    <div class="min-w-0">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div @class(['mt-0.5' => $title])>{{ $slot }}</div>
    </div>
</div>
