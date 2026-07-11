@props([
    'name' => null,
    'href' => null,
    'summary' => null,
])

<a href="{{ $href }}" {{ $attributes->class('group rounded-2xl bg-surface hairline p-5') }}>
    <h3 class="font-semibold group-hover:text-primary transition-colors">{{ $name }}</h3>
    @if($summary)
        <p class="text-sm text-muted-foreground mt-2">{{ $summary }}</p>
    @endif
</a>
