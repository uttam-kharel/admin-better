@props([
    'href' => '#',
])

<a href="{{ $href }}" {{ $attributes->class('inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors mb-6') }}>
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    {{ $slot }}
</a>
