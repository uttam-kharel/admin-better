@props([
    'title' => null,
    'message' => null,
])

<div {{ $attributes->class('rounded-2xl bg-surface hairline p-8 text-center') }}>
    <div class="size-14 rounded-full bg-secondary-soft text-secondary grid place-items-center mx-auto mb-5">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        @endisset
    </div>

    @if ($title)
        <h3 class="text-xl font-bold">{{ $title }}</h3>
    @endif

    @if ($message)
        <p class="text-muted-foreground mt-2 text-sm">{{ $message }}</p>
    @endif

    {{ $slot }}
</div>
