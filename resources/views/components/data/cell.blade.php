@props([
    'align' => 'left',
    'header' => false,
])

@php
    $alignClass = match ($align) {
        'right'  => 'text-right',
        'center' => 'text-center',
        default  => 'text-left',
    };

    $classes = $header
        ? "px-4 py-3 font-semibold $alignClass"
        : "px-4 py-3 align-top $alignClass";
@endphp

@if ($header)
    <th {{ $attributes->class($classes) }}>{{ $slot }}</th>
@else
    <td {{ $attributes->class($classes) }}>{{ $slot }}</td>
@endif
