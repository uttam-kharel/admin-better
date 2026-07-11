@props([
    'title' => null,
    'description' => null,
    'size' => 'md',
])

@php
    $padding = $size === 'sm' ? 'py-8' : ($size === 'lg' ? 'py-24' : 'py-16');
@endphp

<div {{ $attributes->class("col-span-full text-center $padding") }}>
    @isset($icon)
        <div class="mx-auto mb-4 text-muted-foreground/30 [&>svg]:mx-auto [&>svg]:h-16 [&>svg]:w-16">
            {{ $icon }}
        </div>
    @endisset

    @if ($title)
        <p class="text-muted-foreground text-lg font-medium">{{ $title }}</p>
    @endif

    @if ($description)
        <p class="text-sm text-muted-foreground/70 mt-1">{{ $description }}</p>
    @endif

    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset

    {{ $slot }}
</div>
