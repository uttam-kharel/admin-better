@props([
    'href' => '#',
])

<a href="{{ $href }}" {{ $attributes->class('inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors mb-6') }}>
    @svg('lucide-arrow-left', 'h-4 w-4')
    {{ $slot }}
</a>
