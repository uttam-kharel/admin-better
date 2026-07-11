@props([
    'label' => null,
])

@if ($label)
    <div {{ $attributes->class('flex items-center gap-3 my-4') }}>
        <span class="h-px flex-1 bg-border"></span>
        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">{{ $label }}</span>
        <span class="h-px flex-1 bg-border"></span>
    </div>
@else
    <hr {{ $attributes->class('border-0 h-px bg-border my-4') }}>
@endif
