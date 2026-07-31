@props([
    'items' => [],
])

<div {{ $attributes->class('flex flex-wrap gap-x-6 gap-y-2 text-sm text-muted-foreground') }}>
    @foreach($items as $item)
        @if($item['value'] ?? null)
            <span class="inline-flex items-center gap-1.5">
                @svg('lucide-' . $item['icon'], 'h-4 w-4')
                @if($item['prefix'] ?? null){{ $item['prefix'] }}&nbsp;@endif{{ $item['value'] }}
            </span>
        @endif
    @endforeach
</div>
