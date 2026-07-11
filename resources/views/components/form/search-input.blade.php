@props([
    'type' => 'search',
    'variant' => 'public',
])

@php
    $field = $variant === 'admin'
        ? 'w-full pl-9 pr-3 py-2 text-sm rounded-md bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30'
        : 'w-full rounded-md bg-surface hairline pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring';

    $iconPos = $variant === 'admin' ? 'left-3' : 'left-3.5';
@endphp

<div class="relative">
    <svg class="absolute {{ $iconPos }} top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none"
        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
    </svg>

    <input type="{{ $type }}" {{ $attributes->class($field) }}>
</div>
