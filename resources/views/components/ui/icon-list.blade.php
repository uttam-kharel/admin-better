@props([
    'items' => [],
    'columns' => 2,
])

<ul {{ $attributes->class('gap-3 text-sm') ->class(match(true) {
    $columns >= 3 => 'grid sm:grid-cols-2 lg:grid-cols-3',
    $columns >= 2 => 'grid sm:grid-cols-2',
    default => 'space-y-2',
}) }}>
    @foreach($items as $item)
        <li class="flex items-start gap-2">
            @svg('lucide-check-circle', 'h-4 w-4 text-secondary shrink-0 mt-0.5')
            {{ $item }}
        </li>
    @endforeach
</ul>
