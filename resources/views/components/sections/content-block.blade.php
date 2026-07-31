@props([
    'block' => [],
    'title' => null,
])

@php $type = $block['type'] ?? ''; $d = $block['data'] ?? []; @endphp

@switch($type)
    @case('hero')
        <section class="bg-gradient-to-b from-primary-soft to-background">
            <div class="container-page py-12 md:py-16">
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $d['title'] ?? $title }}</h1>
                @if(isset($d['subtitle']))
                    <p class="mt-4 text-lg text-muted-foreground max-w-2xl">{{ $d['subtitle'] }}</p>
                @endif
            </div>
        </section>
        @if(isset($d['image']))
            <section class="container-page -mt-10">
                <div class="aspect-[16/9] rounded-3xl overflow-hidden hairline">
                    <img src="{{ $d['image'] }}" alt="" class="size-full object-cover" loading="lazy" />
                </div>
            </section>
        @endif
        @break

    @case('richText')
        <section class="container-page section-y">
            <div class="max-w-3xl mx-auto">
                <div class="prose prose-lg max-w-none">
                    {!! $d['html'] ?? '' !!}
                </div>
            </div>
        </section>
        @break

    @case('features')
        <section class="bg-surface-muted section-y">
            <div class="container-page">
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-border rounded-2xl overflow-hidden hairline">
                    @foreach(($d['items'] ?? []) as $idx => $item)
                        <div class="bg-surface p-8">
                            <p class="text-secondary font-bold text-2xl mb-3">0{{ $idx + 1 }}</p>
                            <h3 class="font-semibold mb-2">{{ $item['title'] }}</h3>
                            <p class="text-sm text-muted-foreground leading-relaxed">{{ $item['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @break

    @case('cta')
        <section class="container-page section-y">
            <div class="rounded-3xl bg-gradient-to-br from-secondary to-primary text-primary-foreground p-8 md:p-14 text-center">
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight">{{ $d['title'] ?? '' }}</h2>
                @if(isset($d['subtitle']))
                    <p class="mt-4 text-primary-foreground/80 max-w-xl mx-auto">{{ $d['subtitle'] }}</p>
                @endif
                <a href="{{ $d['cta_url'] ?? $d['ctaUrl'] ?? '#' }}" wire:navigate class="mt-6 inline-flex items-center gap-2 rounded-md bg-surface px-6 py-3 text-sm font-semibold text-foreground hover:bg-background transition-colors">
                    {{ $d['cta_label'] ?? $d['ctaLabel'] ?? 'Learn More' }}
                    @svg('lucide-arrow-right', 'h-4 w-4')
                </a>
            </div>
        </section>
        @break

    @case('image')
        <section class="container-page section-y">
            <figure>
                <div class="rounded-3xl overflow-hidden hairline">
                    <img src="{{ $d['src'] }}" alt="{{ $d['alt'] ?? '' }}" class="w-full" loading="lazy" />
                </div>
                @if(isset($d['caption']))
                    <figcaption class="text-sm text-muted-foreground mt-3 text-center">{{ $d['caption'] }}</figcaption>
                @endif
            </figure>
        </section>
        @break

    @default
        <section class="container-page section-y">
            <p class="text-muted-foreground">Content block</p>
        </section>
@endswitch
