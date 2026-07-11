@props([
    'cols' => 3,
    'gap' => 'md',
])

@php
    $colClasses = match ((int) $cols) {
        2 => 'grid-cols-1 sm:grid-cols-2',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    };

    $gapClasses = match ($gap) {
        'sm' => 'gap-3',
        'lg' => 'gap-6',
        'xl' => 'gap-8',
        default => 'gap-4',
    };
@endphp

<div {{ $attributes->class("grid {$colClasses} {$gapClasses}") }}>
    {{ $slot }}
</div>
