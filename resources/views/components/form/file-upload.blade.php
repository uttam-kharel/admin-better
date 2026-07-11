@props([
    'id' => null,
    'label' => 'Choose a file',
    'hint' => null,
    'error' => null,
])

@php
    $id = $id ?? 'file-' . \Illuminate\Support\Str::random(6);
@endphp

<div class="flex flex-col gap-1.5">
    <label for="{{ $id }}"
        class="flex items-center gap-3 px-4 py-3 rounded-lg border-2 border-dashed border-border bg-background hover:bg-muted/50 hover:border-primary/40 cursor-pointer transition-colors">
        <svg class="h-5 w-5 text-muted-foreground shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L8 8m4-4l4 4M4 20h16" />
        </svg>
        <span class="text-sm text-muted-foreground">{{ $label }}</span>
        <input id="{{ $id }}" type="file" {{ $attributes->class('sr-only') }}>
    </label>

    @if ($error)
        <x-form.error :messages="$error" />
    @elseif ($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
