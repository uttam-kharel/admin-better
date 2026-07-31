<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-10 md:py-14 grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <x-navigation.back-link :href="route('departments.index')">All departments</x-navigation.back-link>
                <div class="flex items-center gap-3 mb-3">
                    <div class="size-10 rounded-xl bg-primary text-primary-foreground grid place-items-center">
                        @svg('lucide-' . ($lucideMap[$department->icon ?? ''] ?? $department->icon ?? 'building-2'), 'h-5 w-5')
                    </div>
                    <p class="text-eyebrow">{{ $department->tagline ?? 'Specialty' }}</p>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $department->name }}</h1>
                <p class="mt-4 text-lg text-muted-foreground leading-relaxed">{{ $department->description }}</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('appointment') }}" class="inline-flex rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground">
                        Book appointment
                    </a>
                    <a href="{{ route('doctors.index') }}" class="inline-flex rounded-md hairline bg-surface px-5 py-3 text-sm font-semibold">
                        See doctors
                    </a>
                </div>
            </div>
            <div class="aspect-[5/4] rounded-3xl overflow-hidden hairline">
                <img src="{{ $department->image }}" alt="{{ $department->name }}" class="size-full object-cover" />
            </div>
        </div>
    </section>

    <section class="container-page py-12 grid lg:grid-cols-2 gap-8">
        @if($department->treatments)
            <div class="rounded-2xl bg-surface hairline p-7">
                <h2 class="text-xl font-bold mb-5">Treatments offered</h2>
                <x-ui.icon-list :items="$department->treatments" />
            </div>
        @endif
        @if($department->facilities)
            <div class="rounded-2xl bg-surface hairline p-7">
                <h2 class="text-xl font-bold mb-5">Facilities</h2>
                <x-ui.icon-list :items="$department->facilities" />
            </div>
        @endif
    </section>

    @if($doctors->count() > 0)
        <section class="container-page py-8">
            <h2 class="text-2xl font-bold mb-6">Our {{ $department->name }} specialists</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($doctors as $doc)
                    <x-sections.doctor-card
                        :photo="$doc->photo"
                        :name="$doc->name"
                        :href="route('doctors.show', $doc->slug)"
                        :designation="$doc->designation"
                    />
                @endforeach
            </div>
        </section>
    @endif
</div>
