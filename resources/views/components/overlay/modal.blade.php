@props([
    'title' => null,
    'size' => '2xl',
    'close' => null,
])

@php
    $sizes = [
        'sm'  => 'sm:max-w-sm',
        'md'  => 'sm:max-w-md',
        'lg'  => 'sm:max-w-lg',
        'xl'  => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
    ];
    $maxWidth = $sizes[$size] ?? $sizes['2xl'];
@endphp

<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
    <button
        type="button"
        aria-label="Close"
        @if($close) wire:click="{{ $close }}" @endif
        class="absolute inset-0 bg-foreground/50 backdrop-blur-sm"
    ></button>

    <div {{ $attributes->class("relative bg-surface rounded-t-2xl sm:rounded-2xl w-full {$maxWidth} max-h-[90vh] overflow-hidden flex flex-col shadow-elevated animate-fade-up") }}>
        @if ($title || isset($header))
            <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-border shrink-0">
                <div class="min-w-0">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h2 class="text-lg font-semibold text-foreground truncate">{{ $title }}</h2>
                    @endisset
                </div>
                @if ($close)
                    <button type="button" wire:click="{{ $close }}" aria-label="Close"
                        class="size-8 grid place-items-center rounded-lg text-muted-foreground hover:bg-muted transition-colors shrink-0">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        @endif

        <div class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-border shrink-0">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
