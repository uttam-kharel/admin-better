@props([
    'clickable' => false,
])

<tr {{ $attributes->class($clickable ? 'hover:bg-muted/30 transition-colors cursor-pointer' : 'hover:bg-muted/30 transition-colors') }}>
    {{ $slot }}
</tr>
