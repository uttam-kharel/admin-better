@props([
    'photo' => null,
    'name' => null,
    'href' => null,
    'designation' => null,
    'meta' => null,
    'aspect' => '4/5',
])

@php
    $classes = 'group' . ($href ? '' : '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        <div class="aspect-[{{ $aspect }}] rounded-2xl overflow-hidden hairline bg-muted mb-4">
            <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy" class="size-full object-cover group-hover:scale-105 transition-transform duration-500" />
        </div>
        <h3 class="font-semibold group-hover:text-primary transition-colors">{{ $name }}</h3>
        @if($designation)
            <p class="text-sm text-secondary font-medium mt-0.5">{{ $designation }}</p>
        @endif
        @if($meta)
            <p class="text-xs text-muted-foreground mt-1">{{ $meta }}</p>
        @endif
    </a>
@else
    <div {{ $attributes->class($classes) }}>
        <div class="aspect-[{{ $aspect }}] rounded-2xl overflow-hidden hairline bg-muted">
            <img src="{{ $photo }}" alt="{{ $name }}" class="size-full object-cover" />
        </div>
        <h3 class="font-semibold mt-3">{{ $name }}</h3>
        @if($designation)
            <p class="text-sm text-secondary font-medium mt-1">{{ $designation }}</p>
        @endif
        @if($meta)
            <p class="text-xs text-muted-foreground mt-1">{{ $meta }}</p>
        @endif
    </div>
@endif
