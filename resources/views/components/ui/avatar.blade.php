@props([
    'src' => null,
    'alt' => '',
    'size' => 'md',
    'initials' => null,
])

@php
    $sizes = [
        'xs' => 'size-7 text-xs',
        'sm' => 'size-9 text-sm',
        'md' => 'size-11 text-sm',
        'lg' => 'size-14 text-base',
        'xl' => 'size-20 text-xl',
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $alt }}"
        {{ $attributes->class("$sizeClasses rounded-full object-cover border border-border") }}>
@else
    <span {{ $attributes->class("$sizeClasses rounded-full bg-primary-soft text-primary grid place-items-center font-semibold shrink-0") }}>
        {{ $initials ?? ($slot->isNotEmpty() ? $slot : '?') }}
    </span>
@endif
