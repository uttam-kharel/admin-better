@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-md font-semibold transition-opacity focus:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:opacity-60 disabled:pointer-events-none';

    $variants = [
        'primary'     => 'bg-primary text-primary-foreground shadow-card hover:opacity-90',
        'secondary'   => 'bg-secondary text-secondary-foreground shadow-card hover:opacity-90',
        'outline'     => 'border border-border bg-transparent text-foreground hover:bg-muted',
        'ghost'       => 'bg-transparent text-foreground hover:bg-muted',
        'destructive' => 'bg-destructive text-destructive-foreground shadow-card hover:opacity-90',
        'emergency'   => 'bg-emergency text-emergency-foreground shadow-card hover:opacity-90',
    ];

    $sizes = [
        'sm' => 'text-xs px-3 py-1.5',
        'md' => 'text-sm px-4 py-2',
        'lg' => 'text-sm px-6 py-3',
    ];

    $classes = trim($base
        . ' ' . ($variants[$variant] ?? $variants['primary'])
        . ' ' . ($sizes[$size] ?? $sizes['md']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
