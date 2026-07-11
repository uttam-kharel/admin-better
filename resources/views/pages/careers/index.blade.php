<?php

use App\Models\SiteSetting;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JobOpening;


new class extends Component
{
public string $search = '';
    public string $type = '';
    public string $category = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = JobOpening::available()->orderBy('order');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%")
                  ->orWhere('department', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $jobs = $query->paginate(12);

        $categoryCounts = JobOpening::available()
            ->selectRaw('category, count(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category');

        $categories = $categoryCounts->keys()->mapWithKeys(fn ($key) => [
            $key => ucwords(str_replace(['-', '_'], ' ', $key)),
        ])->toArray();

        $types = JobOpening::available()
            ->select('type')
            ->distinct()
            ->whereNotNull('type')
            ->orderBy('type')
            ->pluck('type')
            ->toArray();

        $departments = JobOpening::available()
            ->select('department')
            ->distinct()
            ->whereNotNull('department')
            ->orderBy('department')
            ->pluck('department');

        $siteSetting = SiteSetting::first();
        $careersContent = $siteSetting?->careers_page ?? [];
        $siteName = $siteSetting?->site_name ?? 'Shubham International';

        return $this->view([
            'jobs' => $jobs,
            'categories' => $categories,
            'categoryCounts' => $categoryCounts,
            'types' => $types,
            'departments' => $departments,
            'careersContent' => $careersContent,
        ]);
    }
};

?>
<div>
    {{-- Hero --}}
    <x-ui.page-header
        :eyebrow="$careersContent['hero_eyebrow'] ?? 'Careers'"
        :title="$careersContent['hero_title'] ?? 'Your Career. Our Mission. Together, We Heal.'"
        :subtitle="$careersContent['hero_subtitle'] ?? 'At Shubham International Hospital, we realize that in order to provide our community with excellent care, we must begin by providing our team with the same care and appreciation. Explore opportunities to grow professionally in an environment of clinical excellence and compassion.'"
    >
        <div class="mt-8 flex flex-wrap gap-4 items-center">
            <div class="relative flex-1 min-w-[260px] max-w-lg">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input
                    type="search"
                    wire:model.live.debounce="search"
                    placeholder="{{ $careersContent['search_placeholder'] ?? 'Search jobs by title, department, or location…' }}"
                    class="w-full rounded-xl bg-surface hairline pl-10 pr-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    aria-label="Search jobs"
                />
            </div>
            <a href="#openings" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 transition-opacity">
                {{ $careersContent['search_cta'] ?? 'View All Openings' }}
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
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
                    <svg class="text-muted-foreground/30" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </x-slot:icon>
                <x-slot:action>
                    <button wire:click="$set('search', ''); $set('type', ''); $set('category', '')" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
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
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $job->location }}
                            </span>
                            @if($job->department)
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    {{ $job->department }}
                                </span>
                            @endif
                            @if($job->salary_range)
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    {{ $job->salary_range }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-muted-foreground leading-relaxed line-clamp-3 mb-4">
                            {{ $job->description }}
                        </p>
                        @if($job->closing_date)
                            <p class="text-xs text-muted-foreground/70 mb-4 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                Closes {{ $job->closing_date->format('M j, Y') }}
                            </p>
                        @endif
                        <div class="mt-auto pt-2">
                            <a href="{{ route('careers.show', $job->slug) }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                                View Details
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
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
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
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
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
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
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
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
                        <svg class="h-28 w-28 text-white/40" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
