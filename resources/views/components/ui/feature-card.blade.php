@props([
    'title' => null,
    'text' => null,
    'align' => 'center',
])

@php
    $alignClass = $align === 'left' ? 'text-left' : 'text-center';
    $iconAlign = $align === 'left' ? '' : 'mx-auto';
@endphp

<div {{ $attributes->class("rounded-2xl bg-surface hairline p-6 $alignClass") }}>
    @isset($icon)
        <div class="size-12 rounded-xl bg-primary-soft text-primary grid place-items-center mb-4 {{ $iconAlign }} [&>svg]:h-6 [&>svg]:w-6">
            {{ $icon }}
        </div>
    @endisset

    @if ($title)
        <h3 class="font-semibold">{{ $title }}</h3>
    @endif

    @if ($text)
        <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $text }}</p>
    @endif

    {{ $slot }}
</div>
