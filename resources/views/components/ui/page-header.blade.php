@props([
    'eyebrow' => null,
    'title' => null,
    'subtitle' => null,
])

<section {{ $attributes->class('bg-gradient-to-b from-primary-soft to-background') }}>
    <div class="container-page py-14 md:py-20">
        @if ($eyebrow)
            <p class="text-eyebrow mb-3">{{ $eyebrow }}</p>
        @endif

        @if ($title)
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $title }}</h1>
        @endif

        @if ($subtitle)
            <p class="mt-4 text-lg text-muted-foreground max-w-2xl leading-relaxed">{{ $subtitle }}</p>
        @endif

        @isset($actions)
            <div class="mt-6 flex flex-wrap gap-3">{{ $actions }}</div>
        @endisset

        {{ $slot }}
    </div>
</section>
