@props([
    'href' => '#',
])

<a href="{{ $href }}" {{ $attributes->class('inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline') }}>
    {{ $slot }}
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>
</a>
