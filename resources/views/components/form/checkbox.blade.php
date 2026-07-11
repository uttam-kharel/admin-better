@props([
    'label' => null,
    'id' => null,
])

@php
    $id = $id ?? 'checkbox-' . \Illuminate\Support\Str::random(6);
@endphp

<label for="{{ $id }}" class="inline-flex items-center gap-2.5 cursor-pointer select-none">
    <input
        id="{{ $id }}"
        type="checkbox"
        {{ $attributes->class('size-4 rounded border-border text-primary focus:ring-2 focus:ring-primary/30') }}
    >
    @if ($label)
        <span class="text-sm text-foreground">{{ $label }}</span>
    @endif
    {{ $slot }}
</label>
