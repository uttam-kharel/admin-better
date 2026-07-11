@props([
    'active' => false,
    'href' => null,
    'variant' => 'bordered',
])

@php
    if ($variant === 'filled') {
        $classes = 'inline-flex items-center gap-1 rounded-full px-4 py-1.5 text-xs font-medium transition-colors '
            . ($active
                ? 'bg-primary text-primary-foreground'
                : 'bg-surface hairline text-muted-foreground hover:text-primary hover:border-primary/30');
    } elseif ($variant === 'tag') {
        $classes = 'inline-flex items-center text-xs font-medium px-3 py-1.5 rounded-full bg-muted text-foreground';
    } else {
        $classes = 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-full border transition-colors '
            . ($active
                ? 'bg-primary text-primary-foreground border-primary'
                : 'bg-surface text-muted-foreground border-border hover:border-primary hover:text-primary');
    }
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@elseif ($variant === 'tag')
    <span {{ $attributes->class($classes) }}>{{ $slot }}</span>
@else
    <button type="button" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
