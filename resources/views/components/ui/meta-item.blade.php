@props([
    'icon',
    'label',
    'value',
])

<div {{ $attributes->class('flex items-start gap-3') }}>
    <div class="size-9 rounded-full bg-primary-soft text-primary grid place-items-center shrink-0">
        @svg('lucide-' . $icon, 'h-4 w-4')
    </div>
    <div>
        <p class="text-xs uppercase tracking-widest text-muted-foreground">{{ $label }}</p>
        <p class="text-sm font-medium">{{ $value }}</p>
    </div>
</div>
