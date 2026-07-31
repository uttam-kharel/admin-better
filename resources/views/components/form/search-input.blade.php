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
    @svg('lucide-search', 'absolute {{ $iconPos }} top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none')

    <input type="{{ $type }}" {{ $attributes->class($field) }}>
</div>
