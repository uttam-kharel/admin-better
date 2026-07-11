@props([
    'label' => null,
    'error' => null,
    'id' => null,
    'hint' => null,
    'placeholder' => null,
    'required' => false,
    'variant' => 'default',
])

@php
    $id = $id ?? 'select-' . \Illuminate\Support\Str::random(6);

    $sizing = $variant === 'admin' ? 'px-3 py-2 rounded-md' : 'px-3 py-2.5 rounded-lg';

    $fieldClasses = "w-full $sizing text-sm bg-background border focus:outline-none focus:ring-2 focus:ring-primary/30 "
        . ($error ? 'border-destructive' : 'border-border');
@endphp

<div class="flex flex-col gap-1.5">
    @if ($label)
        <x-form.label :for="$id" :required="$required">{{ $label }}</x-form.label>
    @endif

    <select id="{{ $id }}" @required($required) {{ $attributes->class($fieldClasses) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>

    @if ($error)
        <x-form.error :messages="$error" />
    @elseif ($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
