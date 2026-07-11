@props([
    'variant' => 'primary',
    'size' => 'md',
    'shape' => 'rounded',
])

@php
    $variants = [
        'primary'   => 'bg-primary-soft text-primary',
        'solid'     => 'bg-primary text-primary-foreground',
        'secondary' => 'bg-secondary-soft text-secondary',
        'emergency' => 'bg-emergency-soft text-emergency',
        'muted'     => 'bg-muted text-muted-foreground',
    ];

    $sizes = [
        'sm' => 'size-9',
        'md' => 'size-11',
        'lg' => 'size-12',
        'xl' => 'size-14',
    ];

    $shapes = [
        'rounded' => 'rounded-xl',
        'square'  => 'rounded-lg',
        'circle'  => 'rounded-full',
    ];

    $classes = 'grid place-items-center shrink-0 '
        . ($sizes[$size] ?? $sizes['md']) . ' '
        . ($shapes[$shape] ?? $shapes['rounded']) . ' '
        . ($variants[$variant] ?? $variants['primary']);
@endphp

<span {{ $attributes->class($classes) }}>
    {{ $slot }}
</span>
