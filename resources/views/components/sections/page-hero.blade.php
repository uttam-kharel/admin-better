@props([
    'eyebrow' => null,
    'title' => null,
    'subtitle' => null,
    'muted' => true,
])

<section class="bg-gradient-to-b from-primary-soft to-background">
    <div class="container-page py-12 md:py-16">
        @if($eyebrow)
            <p class="text-eyebrow mb-3">{{ $eyebrow }}</p>
        @endif
        <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-3 text-lg text-muted-foreground max-w-2xl">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
