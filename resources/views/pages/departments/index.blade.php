<div>
    <x-sections.page-hero eyebrow="Specialties" title="Centers of excellence" subtitle="Multidisciplinary teams delivering specialized care across 40+ medical fields." />

    <section class="container-page py-12">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($departments as $d)
                <a href="{{ route('departments.show', $d->slug) }}" class="group rounded-2xl overflow-hidden hairline bg-surface hover:shadow-card transition-all">
                    <div class="aspect-[16/10] overflow-hidden bg-muted">
                        <img src="{{ $d->image }}" alt="{{ $d->name }}" loading="lazy" class="size-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="size-10 rounded-xl bg-primary-soft text-primary grid place-items-center">
                                @svg('lucide-' . ($lucideMap[$d->icon ?? ''] ?? $d->icon ?? 'building-2'), 'h-5 w-5')
                            </div>
                            <p class="text-eyebrow">{{ $d->tagline ?? 'Specialty' }}</p>
                        </div>
                        <h3 class="text-xl font-semibold group-hover:text-primary transition-colors">{{ $d->name }}</h3>
                        <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $d->description }}</p>
                        <p class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-primary">
                            Explore @svg('lucide-chevron-right', 'h-4 w-4')
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</div>
