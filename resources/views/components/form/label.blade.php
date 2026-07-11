@props([
    'for' => null,
    'required' => false,
])

<label @if($for) for="{{ $for }}" @endif {{ $attributes->class('block text-xs font-semibold text-foreground/80') }}>
    {{ $slot }}
    @if ($required)
        <span class="text-destructive">*</span>
    @endif
</label>
