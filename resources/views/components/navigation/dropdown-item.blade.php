@props([
    'href' => null,
])

@php
    $classes = 'flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-foreground/80 hover:bg-muted hover:text-primary transition-colors';
@endphp

@if ($href)
    <a href="{{ $href }}" role="menuitem" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="button" role="menuitem" {{ $attributes->class("w-full text-left {$classes}") }}>{{ $slot }}</button>
@endif
