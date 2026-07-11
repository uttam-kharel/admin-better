@props([
    'label' => null,
    'padding' => 'md',
])

@php
    $paddings = [
        'sm' => 'p-5',
        'md' => 'p-6',
        'lg' => 'p-8',
    ];
    $paddingClass = $paddings[$padding] ?? $paddings['md'];
@endphp

<div {{ $attributes->class("rounded-2xl bg-surface hairline $paddingClass") }}>
    @if ($label || isset($icon))
        <div class="flex items-center gap-2 text-eyebrow mb-3">
            @isset($icon)
                <span class="text-secondary [&>svg]:h-4 [&>svg]:w-4">{{ $icon }}</span>
            @endisset
            {{ $label }}
        </div>
    @endif

    {{ $slot }}
</div>
