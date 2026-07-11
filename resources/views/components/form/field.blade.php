@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'for' => null,
    'required' => false,
])

<div {{ $attributes->class('flex flex-col gap-1.5') }}>
    @if ($label)
        <x-form.label :for="$for" :required="$required">{{ $label }}</x-form.label>
    @endif

    {{ $slot }}

    @if ($error)
        <x-form.error :messages="$error" />
    @elseif ($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
