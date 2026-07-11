@props([
    'label' => null,
    'error' => null,
    'id' => null,
    'hint' => null,
    'rows' => 4,
    'required' => false,
    'variant' => 'default',
])

@php
    $id = $id ?? 'textarea-' . \Illuminate\Support\Str::random(6);

    $sizing = $variant === 'admin'
        ? 'px-3 py-2 rounded-md'
        : 'px-3 py-2.5 rounded-lg resize-none';

    $fieldClasses = "w-full $sizing text-sm bg-background border focus:outline-none focus:ring-2 focus:ring-primary/30 "
        . ($error ? 'border-destructive' : 'border-border');
@endphp

<div class="flex flex-col gap-1.5">
    @if ($label)
        <x-form.label :for="$id" :required="$required">{{ $label }}</x-form.label>
    @endif

    <textarea id="{{ $id }}" rows="{{ $rows }}" @required($required) {{ $attributes->class($fieldClasses) }}>{{ $slot }}</textarea>

    @if ($error)
        <x-form.error :messages="$error" />
    @elseif ($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
