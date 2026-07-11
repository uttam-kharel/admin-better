@props([
    'messages' => null,
])

@php
    $items = is_string($messages) ? [$messages] : (array) ($messages ?? []);
@endphp

@if (!empty($items))
    @foreach ($items as $message)
        <span {{ $attributes->class('block mt-1 text-xs text-destructive') }}>{{ $message }}</span>
    @endforeach
@endif
