@props([
    'href' => null,
    'src' => null,
    'alt' => '',
    'aspect' => '16/10',
    'title' => null,
    'subtitle' => null,
])

@php
    $aspects = [
        '16/10' => 'aspect-[16/10]',
        '16/9'  => 'aspect-video',
        '4/3'   => 'aspect-[4/3]',
        '4/5'   => 'aspect-[4/5]',
        '1/1'   => 'aspect-square',
    ];
    $aspectClass = $aspects[$aspect] ?? 'aspect-[16/10]';

    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class('group block rounded-2xl overflow-hidden bg-surface hairline') }}>
    <div class="{{ $aspectClass }} overflow-hidden bg-muted">
        @if ($src)
            <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy"
                class="size-full object-cover group-hover:scale-105 transition-transform duration-500">
        @endif
    </div>

    @if ($title || $subtitle || isset($body))
        <div class="p-5">
            @if ($subtitle)
                <p class="text-eyebrow mb-2">{{ $subtitle }}</p>
            @endif
            @if ($title)
                <h3 class="font-semibold leading-snug group-hover:text-primary transition-colors">{{ $title }}</h3>
            @endif
            @isset($body)
                <div class="mt-2 text-sm text-muted-foreground leading-relaxed">{{ $body }}</div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</{{ $tag }}>
