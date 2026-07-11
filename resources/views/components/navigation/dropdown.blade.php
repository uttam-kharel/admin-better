@props([
    'width' => 'w-64',
    'align' => 'left',
])

@php
    $alignClasses = match ($align) {
        'right'  => 'right-0',
        'center' => 'left-1/2 -translate-x-1/2',
        default  => 'left-0',
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button type="button" @click="open = !open" class="inline-flex items-center gap-1">
        {{ $trigger }}
    </button>

    <div
        x-show="open"
        x-transition
        x-cloak
        role="menu"
        class="absolute {{ $alignClasses }} top-full mt-2 z-50"
    >
        <div {{ $attributes->class("rounded-xl bg-popover hairline shadow-elevated p-2 {$width} animate-fade-up") }}>
            {{ $slot }}
        </div>
    </div>
</div>
