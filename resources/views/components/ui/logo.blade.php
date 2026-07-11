@props([
    'label' => null,
    'logoText' => null,
    'size' => 'md',
    'tone' => 'primary',
])

@php
    $sizes = [
        'sm' => 'size-8 text-xs',
        'md' => 'size-9 text-sm',
        'lg' => 'size-10 text-base',
    ];
    $logoClasses = $sizes[$size] ?? $sizes['md'];

    $tones = [
        'primary' => 'bg-primary text-primary-foreground',
        'secondary' => 'bg-secondary text-secondary-foreground',
    ];
    $toneClasses = $tones[$tone] ?? $tones['primary'];
@endphp

<div class="flex items-center gap-2">
    <div class="{{ $logoClasses }} rounded-lg {{ $toneClasses }} grid place-items-center font-bold">{{ $logoText ?? 'S' }}</div>
    <div class="font-bold tracking-tight {{ $size === 'lg' ? 'text-lg' : 'text-base' }}">{{ $label ?? 'Shubham International' }}</div>
</div>
