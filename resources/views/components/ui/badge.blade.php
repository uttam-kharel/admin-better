@props([
    'variant' => 'neutral',
    'dot' => false,
])

@php
    $variants = [
        // Neutral — archived, planned, backlog, cancelled, inactive
        'neutral' => 'bg-muted text-muted-foreground',
        // Blue — new, todo
        'primary' => 'bg-primary-soft text-primary',
        // Green — active, completed, confirmed, approved, hired
        'success' => 'bg-emerald-100 text-emerald-800',
        // Yellow — pending, idle
        'warning' => 'bg-amber-100 text-amber-800',
        // Sky — responded, reviewed, informational
        'info'    => 'bg-sky-100 text-sky-800',
        // Red — failed, rejected, cancelled, blocked
        'danger'  => 'bg-emergency-soft text-emergency',
        // Cyan — running
        'cyan'    => 'bg-cyan-100 text-cyan-800',
        // Orange — paused
        'orange'  => 'bg-orange-100 text-orange-800',
        // Indigo — in_progress
        'indigo'  => 'bg-indigo-100 text-indigo-800',
        // Violet — in_review, interviewed
        'violet'  => 'bg-violet-100 text-violet-800',
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
