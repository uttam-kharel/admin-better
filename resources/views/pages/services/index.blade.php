<div>
    <x-sections.page-hero eyebrow="Services" title="Comprehensive care, end to end" />

    <section class="container-page py-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($services as $service)
            <a href="{{ route('services.show', $service->slug) }}" class="group rounded-2xl bg-surface hairline p-6 hover:shadow-card transition-all">
                <div class="size-11 rounded-xl bg-primary-soft text-primary grid place-items-center mb-4">
                    @svg('lucide-' . ($lucideMap[$service->icon ?? ''] ?? $service->icon ?? 'stethoscope'), 'h-5 w-5')
                </div>
                <h3 class="text-lg font-semibold group-hover:text-primary transition-colors">{{ $service->name }}</h3>
                <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $service->summary }}</p>
            </a>
        @endforeach
    </section>
</div>
