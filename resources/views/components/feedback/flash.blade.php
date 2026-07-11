@props([
    'key' => 'message',
    'variant' => 'success',
    'dismiss' => false,
])

@php
    $variants = [
        'success' => 'border-secondary/30 bg-secondary-soft text-secondary',
        'error'   => 'border-destructive/30 bg-destructive/10 text-destructive',
        'info'    => 'border-primary/30 bg-primary-soft text-primary',
        'warning' => 'border-emergency/30 bg-emergency-soft text-emergency',
    ];
    $classes = 'rounded-md border px-4 py-3 text-sm ' . ($variants[$variant] ?? $variants['success']);
@endphp

@if (session()->has($key))
    <div
        @if($dismiss)
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
        @endif
        role="status"
        {{ $attributes->class($classes) }}
    >
        {{ session($key) }}
    </div>
@endif
