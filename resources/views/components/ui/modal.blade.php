@props([
    'title' => null,
    'maxWidth' => 'md',
    'close' => null,
])

@php
    $max = match ($maxWidth) {
        'sm' => 'sm:max-w-sm',
        'lg' => 'sm:max-w-4xl',
        'xl' => 'sm:max-w-6xl',
        default => 'sm:max-w-2xl',
    };
    // `close` is either a Livewire method name (wire:click) or a JS expression (@click / x-on:close).
    $isJs = str_contains((string) $close, '$') || str_contains((string) $close, '(') || str_contains((string) $close, '()');
@endphp

<div {{ $attributes->merge(['class' => 'fixed inset-0 z-50 flex items-end sm:items-center justify-center']) }} x-data x-cloak>
    @if($close)
        @if($isJs)
            <button type="button" aria-label="Close" @click="{{ $close }}" class="absolute inset-0 bg-foreground/50 backdrop-blur-sm"></button>
        @else
            <button type="button" aria-label="Close" wire:click="{{ $close }}" class="absolute inset-0 bg-foreground/50 backdrop-blur-sm"></button>
        @endif
    @endif
    <div class="relative bg-surface rounded-t-2xl sm:rounded-2xl w-full {{ $max }} max-h-[90vh] overflow-hidden overscroll-contain flex flex-col shadow-elevated animate-fade-up">
        @if($title)
            <div class="px-6 py-4 border-b border-border flex items-center justify-between shrink-0">
                <h3 class="font-semibold">{{ $title }}</h3>
                @if($close)
                    @if($isJs)
                        <button type="button" @click="{{ $close }}" class="text-muted-foreground hover:text-foreground text-sm">Close</button>
                    @else
                        <button type="button" wire:click="{{ $close }}" class="text-muted-foreground hover:text-foreground text-sm">Close</button>
                    @endif
                @endif
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
