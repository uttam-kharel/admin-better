@props([
    'variant' => 'default',
    'padding' => 'md',
    'hover' => false,
])

@php
    $variants = [
        'default' => 'bg-surface border border-border',
        'soft'    => 'bg-surface hairline',
        'flat'    => 'bg-card',
        'muted'   => 'bg-muted/40 border border-border',
    ];

    $paddings = [
        'none' => '',
        'sm'   => 'p-4',
        'md'   => 'p-6',
        'lg'   => 'p-8',
    ];

    $classes = trim('rounded-xl'
        . ' ' . ($variants[$variant] ?? $variants['default'])
        . ' ' . ($paddings[$padding] ?? $paddings['md'])
        . ' ' . ($hover ? 'hover:shadow-card transition-shadow' : ''));
@endphp

<div {{ $attributes->class($classes) }}>
    {{ $slot }}
</div>
