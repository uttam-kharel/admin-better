@props([
    'variant' => 'neutral',
    'dot' => false,
])

@php
    // All variants use SEMANTIC status tokens (--color-*-soft / --color-*),
    // never raw palette colors — they adapt to the light/dark theme automatically.
    $variants = [
        // Neutral — archived, planned, backlog, cancelled, inactive
        'neutral' => 'bg-muted text-muted-foreground',
        // Blue — new, todo
        'primary' => 'bg-primary-soft text-primary',
        // Green — active, completed, confirmed, approved, hired
        'success' => 'bg-success-soft text-success',
        // Yellow — pending, idle
        'warning' => 'bg-warning-soft text-warning',
        // Sky — responded, reviewed, informational
        'info'    => 'bg-info-soft text-info',
        // Red — failed, rejected, cancelled, blocked
        'danger'  => 'bg-emergency-soft text-emergency',
        // Cyan — running
        'cyan'    => 'bg-cyan-soft text-cyan',
        // Orange — paused
        'orange'  => 'bg-orange-soft text-orange',
        // Indigo — in_progress
        'indigo'  => 'bg-indigo-soft text-indigo',
        // Violet — in_review, interviewed
        'violet'  => 'bg-violet-soft text-violet',
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
