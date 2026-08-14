@props([
    'variant' => 'neutral',
    'dot' => false,
])

@php
    $variants = [
        'neutral' => 'bg-muted text-muted-foreground',
        'primary' => 'bg-primary-soft text-primary',
        'success' => 'bg-emerald-100 text-emerald-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'info'    => 'bg-sky-100 text-sky-800',
        'danger'  => 'bg-emergency-soft text-emergency',
    ];

    $classes = 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold whitespace-nowrap '
        . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->class($classes) }}>
    @if($dot)
        <span class="size-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
