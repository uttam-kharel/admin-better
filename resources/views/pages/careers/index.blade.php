<div>
    {{-- Hero --}}
    <x-ui.page-header
        :eyebrow="$careersContent['hero_eyebrow'] ?? 'Careers'"
        :title="$careersContent['hero_title'] ?? 'Your Career. Our Mission. Together, We Heal.'"
        :subtitle="$careersContent['hero_subtitle'] ?? 'At Shubham International Hospital, we realize that in order to provide our community with excellent care, we must begin by providing our team with the same care and appreciation. Explore opportunities to grow professionally in an environment of clinical excellence and compassion.'"
    >
        <div class="mt-8 flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[260px] max-w-lg">
                <x-form.search-input
                    wire:model.live.debounce="search"
                    placeholder="{{ $careersContent['search_placeholder'] ?? 'Search jobs by title, department, or location…' }}"
                    aria-label="Search jobs"
                />
            </div>
            <a href="#openings" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 transition-opacity">
                {{ $careersContent['search_cta'] ?? 'View All Openings' }}
                @svg('lucide-arrow-right', 'h-4 w-4')
            </a>
        </div>
    </x-ui.page-header>

    {{-- Category Tabs --}}
    <section id="openings" class="container-page pt-12 pb-4">
        <div class="flex flex-wrap gap-2">
            <x-ui.pill :active="$category === ''" wire:click="$set('category', '')">
                All Positions
                <span class="ml-1.5 text-xs opacity-70">({{ array_sum($categoryCounts->toArray()) ?: $jobs->total() }})</span>
            </x-ui.pill>
            @foreach($categories as $key => $label)
                @if(isset($categoryCounts[$key]))
                    <x-ui.pill :active="$category === $key" wire:click="$set('category', '{{ $key }}')">
                        {{ $label }}
                        <span class="ml-1.5 text-xs opacity-70">({{ $categoryCounts[$key] }})</span>
                    </x-ui.pill>
                @endif
            @endforeach
        </div>
        <div class="mt-3 flex flex-wrap gap-2 items-center text-sm text-muted-foreground">
            <span class="text-xs font-medium mr-1">Filter by type:</span>
            <button
                wire:click="$set('type', '')"
                class="px-3 py-1 text-xs font-medium rounded-full border transition-colors {{ $type === '' ? 'bg-primary-soft text-primary border-primary/30' : 'bg-surface text-muted-foreground border-border hover:border-primary/30 hover:text-primary' }}"
            >
                All
            </button>
            @foreach($types as $t)
                <button
                    wire:click="$set('type', '{{ $t }}')"
                    class="px-3 py-1 text-xs font-medium rounded-full border transition-colors capitalize {{ $type === $t ? 'bg-primary-soft text-primary border-primary/30' : 'bg-surface text-muted-foreground border-border hover:border-primary/30 hover:text-primary' }}"
                >
                    {{ str_replace('-', ' ', $t) }}
                </button>
            @endforeach
        </div>
    </section>

    {{-- Job Listings --}}
    <section class="container-page py-8 pb-16">
        @if($jobs->count() === 0)
            <x-feedback.empty-state
                title="No open positions match your criteria."
                description="Try adjusting your search or filters, or check back later for new opportunities."
                size="lg"
            >
                <x-slot:icon>
                    @svg('lucide-briefcase', 'text-muted-foreground/30')
                </x-slot:icon>
                <x-slot:action>
                    <button wire:click="$set('search', ''); $set('type', ''); $set('category', '')" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                        @svg('lucide-rotate-ccw', 'h-4 w-4')
                        Clear all filters
                    </button>
                </x-slot:action>
            </x-feedback.empty-state>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($jobs as $job)
                    <div class="rounded-2xl bg-surface hairline p-6 flex flex-col hover:shadow-card transition-shadow">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="font-semibold text-lg leading-snug">{{ $job->title }}</h3>
                            <span class="shrink-0 text-xs font-medium capitalize px-2.5 py-1 rounded-full bg-primary-soft text-primary">
                                {{ str_replace('-', ' ', $job->type) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-sm text-muted-foreground mb-4">
                            <span class="inline-flex items-center gap-1.5">
                                @svg('lucide-map-pin', 'h-3.5 w-3.5')
                                {{ $job->location }}
                            </span>
                            @if($job->department)
                                <span class="inline-flex items-center gap-1.5">
                                    @svg('lucide-users', 'h-3.5 w-3.5')
                                    {{ $job->department }}
                                </span>
                            @endif
                            @if($job->salary_range)
                                <span class="inline-flex items-center gap-1.5">
                                    @svg('lucide-dollar-sign', 'h-3.5 w-3.5')
                                    {{ $job->salary_range }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-muted-foreground leading-relaxed line-clamp-3 mb-4">
                            {{ $job->description }}
                        </p>
                        @if($job->closing_date)
                            <p class="text-xs text-muted-foreground/70 mb-4 flex items-center gap-1.5">
                                @svg('lucide-calendar', 'h-3.5 w-3.5')
                                Closes {{ $job->closing_date->format('M j, Y') }}
                            </p>
                        @endif
                        <div class="mt-auto pt-2">
                            <a href="{{ route('careers.show', $job->slug) }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                                View Details
                                @svg('lucide-chevron-right', 'h-4 w-4')
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $jobs->links() }}
            </div>
        @endif
    </section>

    {{-- Why Join Us --}}
    @if(!empty($careersContent['why_items']))
        <section class="bg-gradient-to-b from-background to-primary-soft/30">
            <div class="container-page py-16 md:py-20">
                <div class="max-w-2xl mx-auto text-center mb-12">
                    <p class="text-eyebrow mb-3">{{ $careersContent['why_eyebrow'] ?? 'Why Shubham International' }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight">{{ $careersContent['why_title'] ?? 'More than a workplace. A mission.' }}</h2>
                    <p class="mt-4 text-muted-foreground leading-relaxed">
                        {{ $careersContent['why_subtitle'] ?? 'We offer rich opportunities to develop and grow professionally, an environment of excellence in patient care, and the awareness that everything we accomplish is a direct outgrowth of the superb efforts and dedication of our team.' }}
                    </p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($careersContent['why_items'] as $item)
                        <x-ui.feature-card :title="$item['title']" :text="$item['text']">
                            <x-slot:icon>
                                @svg('lucide-' . ($item['icon'] ?? 'users'))
                            </x-slot:icon>
                        </x-ui.feature-card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Contact HR Section --}}
    <section class="container-page py-16 md:py-20">
        <div class="rounded-2xl bg-gradient-to-br from-primary to-primary/90 p-8 md:p-12 text-primary-foreground">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <p class="text-eyebrow text-primary-foreground/70 mb-2">{{ $careersContent['contact_eyebrow'] ?? 'Get in Touch' }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight">{{ $careersContent['contact_title'] ?? 'Have questions about your next career move?' }}</h2>
                    <p class="mt-4 text-primary-foreground/80 leading-relaxed">
                        {{ $careersContent['contact_subtitle'] ?? 'Our HR team is here to help. Whether you need more information about a role, the application process, or life at Shubham International, we would love to hear from you.' }}
                    </p>
                    <div class="mt-8 space-y-4">
                        @if(($careersContent['contact_phone'] ?? false) || ($careersContent['contact_phone_label'] ?? false))
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-lg bg-white/20 grid place-items-center shrink-0">
                                @svg('lucide-phone', 'h-5 w-5')
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ $careersContent['contact_phone_label'] ?? 'Phone' }}</p>
                                <p class="text-sm text-primary-foreground/80">{{ $careersContent['contact_phone'] }}</p>
                            </div>
                        </div>
                        @endif
                        @if(($careersContent['contact_email'] ?? false) || ($careersContent['contact_email_label'] ?? false))
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-lg bg-white/20 grid place-items-center shrink-0">
                                @svg('lucide-mail', 'h-5 w-5')
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ $careersContent['contact_email_label'] ?? 'Email' }}</p>
                                <p class="text-sm text-primary-foreground/80">{{ $careersContent['contact_email'] }}</p>
                            </div>
                        </div>
                        @endif
                        @if(($careersContent['contact_address'] ?? false) || ($careersContent['contact_address_label'] ?? false))
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-lg bg-white/20 grid place-items-center shrink-0">
                                @svg('lucide-map-pin', 'h-5 w-5')
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ $careersContent['contact_address_label'] ?? 'Address' }}</p>
                                <p class="text-sm text-primary-foreground/80">{{ $careersContent['contact_address'] }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="hidden md:flex justify-center">
                    <div class="size-56 rounded-full bg-white/10 grid place-items-center">
                        @svg('lucide-briefcase', 'h-28 w-28 text-white/40')
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
