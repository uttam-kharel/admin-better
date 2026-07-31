<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-12">
            <x-navigation.back-link :href="route('services.index')">All services</x-navigation.back-link>
            <div class="flex items-center gap-3 mb-3">
                <div class="size-11 rounded-xl bg-primary text-primary-foreground grid place-items-center">
                    @svg('lucide-' . ($lucideMap[$service->icon ?? ''] ?? $service->icon ?? 'stethoscope'), 'h-5 w-5')
                </div>
                <p class="text-eyebrow">{{ $service->summary }}</p>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $service->name }}</h1>
            <p class="mt-4 text-lg text-muted-foreground max-w-3xl leading-relaxed">{{ $service->description }}</p>
        </div>
    </section>
    <section class="container-page py-12">
        <h2 class="text-2xl font-bold mb-6">Related services</h2>
        <div class="grid sm:grid-cols-3 gap-5">
            @foreach($related as $rel)
                <x-sections.related-service
                    :name="$rel->name"
                    :href="route('services.show', $rel->slug)"
                    :summary="$rel->summary"
                />
            @endforeach
        </div>
    </section>
</div>
