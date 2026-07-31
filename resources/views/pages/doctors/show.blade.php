<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-10 md:py-14">
            <x-navigation.back-link :href="route('doctors.index')">All doctors</x-navigation.back-link>
            <div class="grid lg:grid-cols-[280px_1fr] gap-8 md:gap-12">
                <div>
                    <div class="aspect-[4/5] rounded-2xl overflow-hidden hairline bg-muted">
                        <img src="{{ $doctor->photo }}" alt="{{ $doctor->name }}" class="size-full object-cover" />
                    </div>
                </div>
                <div>
                    <p class="text-eyebrow mb-2">{{ $doctor->department }}</p>
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight">{{ $doctor->name }}</h1>
                    <p class="mt-2 text-lg text-secondary font-medium">{{ $doctor->designation }}</p>
                    <p class="mt-5 text-muted-foreground leading-relaxed max-w-2xl">{{ $doctor->bio }}</p>

                    <div class="mt-8 grid sm:grid-cols-2 gap-5 max-w-2xl">
                        <x-ui.meta-item icon="stethoscope" label="Experience" :value="$doctor->experience_years . '+ years'" />
                        @if($doctor->languages)
                            <x-ui.meta-item icon="languages" label="Languages" :value="is_array($doctor->languages) ? implode(', ', $doctor->languages) : $doctor->languages" />
                        @endif
                    </div>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('appointment') }}?doctor={{ $doctor->slug }}" class="inline-flex items-center rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground">
                            Book appointment
                        </a>
                        @if($doctor->department_slug)
                            <a href="{{ route('departments.show', $doctor->department_slug) }}" class="inline-flex items-center rounded-md hairline bg-surface px-5 py-3 text-sm font-semibold">
                                View department
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container-page py-12 grid lg:grid-cols-3 gap-8">
        @if($doctor->qualifications)
            <x-ui.info-card icon="graduation-cap" title="Qualifications">
                <ul class="space-y-2 text-sm text-muted-foreground">
                    @foreach($doctor->qualifications as $q)
                        <li>&middot; {{ $q }}</li>
                    @endforeach
                </ul>
            </x-ui.info-card>
        @endif

        @if($doctor->expertise)
            <x-ui.info-card icon="stethoscope" title="Areas of expertise">
                <div class="flex flex-wrap gap-2">
                    @foreach($doctor->expertise as $exp)
                        <span class="rounded-full bg-secondary-soft text-secondary text-xs font-medium px-3 py-1.5">{{ $exp }}</span>
                    @endforeach
                </div>
            </x-ui.info-card>
        @endif

        @if($doctor->schedule)
            <x-ui.info-card icon="calendar" title="Consultation schedule">
                <ul class="space-y-3 text-sm">
                    @foreach($doctor->schedule as $s)
                        @php $day = is_array($s) ? $s['day'] : $s->day; $hours = is_array($s) ? $s['hours'] : $s->hours; @endphp
                        <li class="flex justify-between gap-3">
                            <span class="font-medium">{{ $day }}</span>
                            <span class="text-muted-foreground">{{ $hours }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-ui.info-card>
        @endif
    </section>

    @if($doctor->publications)
        <section class="container-page py-8">
            <h2 class="text-2xl font-bold mb-4">Publications</h2>
            <ul class="space-y-3 text-sm text-muted-foreground max-w-3xl">
                @foreach($doctor->publications as $p)
                    <li>&middot; {{ $p }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($related->count() > 0)
        <section class="container-page py-12 mt-8">
            <h2 class="text-2xl font-bold mb-6">Related specialists</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($related as $rel)
                    <x-sections.related-doctor
                        :photo="$rel->photo"
                        :name="$rel->name"
                        :href="route('doctors.show', $rel->slug)"
                        :designation="$rel->designation"
                    />
                @endforeach
            </div>
        </section>
    @endif
</div>
