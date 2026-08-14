@props([
    'label' => null,
    'value' => null,
    'href' => null,
    'delta' => null,
    'deltaLabel' => 'vs previous period',
])

@php
    $tag = $href ? 'a' : 'div';
    $interactive = $href ? 'group hover:shadow-card hover:-translate-y-0.5 transition-[box-shadow,transform]' : '';

    $deltaValue = $delta !== null ? (float) $delta : null;
    $deltaPositive = $deltaValue !== null && $deltaValue > 0;
    $deltaNegative = $deltaValue !== null && $deltaValue < 0;
    $deltaClass = $deltaPositive ? 'text-emerald-600' : ($deltaNegative ? 'text-emergency' : 'text-muted-foreground');
    $deltaIcon = $deltaPositive ? 'lucide-trending-up' : ($deltaNegative ? 'lucide-trending-down' : 'lucide-minus');
    $deltaText = $deltaValue !== null ? (($deltaPositive ? '+' : '').number_format($deltaValue, 1).'%') : '—';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class("block rounded-xl bg-surface border border-border p-5 $interactive") }}>
    @isset($icon)
        <div class="size-10 rounded-lg bg-primary/10 text-primary grid place-items-center mb-4 [&>svg]:h-5 [&>svg]:w-5">
            {{ $icon }}
        </div>
    @endisset

    <p class="text-2xl font-bold tabular-nums">{{ $value }}{{ $slot }}</p>

    <div class="mt-1.5 flex items-center gap-2">
        @if($label)
            <p class="text-sm text-muted-foreground">{{ $label }}</p>
        @endif
        @if($deltaValue !== null)
            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $deltaClass }}">
                @svg($deltaIcon, 'h-3.5 w-3.5')
                {{ $deltaText }}
            </span>
            <span class="text-xs text-muted-foreground/60 hidden sm:inline">{{ $deltaLabel }}</span>
        @endif
    </div>
</{{ $tag }}>
