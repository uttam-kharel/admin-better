@props([
    'variant' => 'primary',
])

@php
    $variants = [
        'primary'     => 'bg-primary-soft text-primary',
        'secondary'   => 'bg-secondary-soft text-secondary',
        'emergency'   => 'bg-emergency-soft text-emergency',
        'muted'       => 'bg-muted text-muted-foreground',
        'solid'       => 'bg-primary text-primary-foreground',
        'destructive' => 'bg-destructive text-destructive-foreground',
    ];

    $classes = 'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold '
        . ($variants[$variant] ?? $variants['primary']);
@endphp

<span {{ $attributes->class($classes) }}>
    {{ $slot }}
</span>
