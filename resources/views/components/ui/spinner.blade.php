@props([
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'size-4 border-2',
        'md' => 'size-6 border-2',
        'lg' => 'size-8 border-[3px]',
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
@endphp

<span
    role="status"
    aria-label="Loading"
    {{ $attributes->class("inline-block animate-spin rounded-full border-current border-t-transparent $sizeClasses") }}
></span>
