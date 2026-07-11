<?php

use App\Models\{HeroSlide, QuickAction, Stat, Service, Department, Doctor, HealthPackage, Treatment, Technology, Testimonial, PatientStory, InsurancePartner, Award, BlogPost, Faq, CmsPage, SiteSetting};
use Livewire\Component;

new class extends Component
{
public array $heroSlides = [];
    public array $quickActions = [];
    public array $stats = [];
    public array $services = [];
    public array $departments = [];
    public array $doctors = [];
    public array $packages = [];
    public array $treatments = [];
    public array $technologies = [];
    public array $testimonials = [];
    public array $stories = [];
    public array $insurance = [];
    public array $awards = [];
    public array $blogs = [];
    public array $faqs = [];
    public ?array $aboutPage = null;
    public ?array $whyChooseUsPage = null;
    public ?array $careerPage = null;
    public ?array $settings = null;
    public ?SiteSetting $siteSetting = null;
    public array $homeSections = [];
    public array $aboutContent = [];
    public array $careerStatsContent = [];
    public array $heroContent = [];

    public function mount(): void
    {
        $this->heroSlides = HeroSlide::orderBy('order')->get()->toArray();
        $this->quickActions = QuickAction::all()->toArray();
        $this->stats = Stat::all()->toArray();
        $this->services = Service::all()->toArray();
        $this->departments = Department::all()->toArray();
        $this->doctors = Doctor::take(4)->get()->toArray();
        $this->packages = HealthPackage::all()->toArray();
        $this->treatments = Treatment::all()->toArray();
        $this->technologies = Technology::all()->toArray();
        $this->testimonials = Testimonial::all()->toArray();
        $this->stories = PatientStory::all()->toArray();
        $this->insurance = InsurancePartner::all()->toArray();
        $this->awards = Award::all()->toArray();
        $this->blogs = BlogPost::latest()->take(3)->get()->toArray();
        $this->faqs = Faq::all()->toArray();
        $this->siteSetting = SiteSetting::first();
        $this->settings = $this->siteSetting?->toArray();
        $this->homeSections = $this->siteSetting?->home_sections ?? [];
        $this->aboutContent = $this->siteSetting?->about ?? [];
        $this->careerStatsContent = $this->siteSetting?->career_stats ?? [];
        $this->heroContent = $this->siteSetting?->hero ?? [];

        // Load CMS pages for dynamic homepage sections
        foreach (['about-us', 'why-choose-us', 'careers'] as $slug) {
            $page = CmsPage::where('slug', $slug)->first();
            if ($page) {
                $this->{$slug === 'about-us' ? 'aboutPage' : ($slug === 'why-choose-us' ? 'whyChooseUsPage' : 'careerPage')} = $page->toArray();
            }
        }
    }

    public function render()
    {
        $siteName = $this->settings['site_name'] ?? 'Shubham International';
        return $this->view()
            ->layout('layouts.public', ['title' => $siteName . ' — Advanced Medical Care for Every Generation']);
    }
};

?>
<div>
    {{-- ============ 1. HERO ============ --}}
    @if(!empty($heroSlides))
        @php $slide = $heroSlides[0]; @endphp
        <section class="relative bg-gradient-to-b from-primary-soft to-background overflow-hidden">
            <div class="container-page pt-10 pb-16 md:pt-16 md:pb-24 grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <div class="min-w-0">
                    <p class="text-eyebrow mb-4">{{ $slide['eyebrow'] }}</p>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.05] text-balance">
                        {{ $slide['title'] }}
                    </h1>
                    <p class="mt-5 text-base md:text-lg text-muted-foreground max-w-xl leading-relaxed text-pretty">
                        {{ $slide['subtitle'] }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ $slide['cta_url'] }}" class="inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3.5 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 transition-opacity">
                            {{ $slide['cta_label'] }}
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                        @if($slide['secondary_cta_label'] ?? false)
                            <a href="{{ $slide['secondary_cta_url'] ?? '/' }}" class="inline-flex items-center rounded-md bg-surface px-6 py-3.5 text-sm font-semibold text-foreground hairline hover:bg-muted transition-colors">
                                {{ $slide['secondary_cta_label'] }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-[4/5] sm:aspect-[5/4] lg:aspect-[4/5] rounded-3xl overflow-hidden hairline">                            <img src="{{ $slide['image'] }}" alt="" class="size-full object-cover" loading="eager" fetchPriority="high" />
                    </div>
                    @if($heroContent)
                        <div class="absolute -bottom-5 -left-5 hidden sm:flex bg-surface rounded-xl shadow-elevated hairline p-5 max-w-xs items-center gap-3">
                            <div class="size-2.5 rounded-full bg-secondary animate-pulse" aria-hidden="true"></div>
                            <div>
                                <p class="text-xs font-semibold tracking-widest uppercase text-muted-foreground">{{ $heroContent['status_label'] ?? 'ER status' }}</p>
                                <p class="text-sm font-medium">{{ $heroContent['wait_label'] ?? 'Avg wait:' }} <span class="text-secondary">{{ $heroContent['wait_value'] ?? '8 min' }}</span></p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 2. QUICK ACTIONS ============ --}}
    @if(!empty($quickActions))
        @php $tones = ['emergency' => 'bg-emergency-soft text-emergency', 'primary' => 'bg-primary-soft text-primary', 'secondary' => 'bg-secondary-soft text-secondary', 'neutral' => 'bg-muted text-foreground'] @endphp
        <section class="container-page -mt-10 md:-mt-12 relative z-10">
            <div class="bg-surface rounded-2xl shadow-elevated hairline grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-border overflow-hidden">
                @foreach($quickActions as $action)
                    <a href="{{ $action['url'] }}" class="p-5 md:p-6 flex items-center gap-4 hover:bg-muted transition-colors group min-w-0">
                        <div class="size-11 rounded-full grid place-items-center shrink-0 {{ $tones[$action['tone']] ?? $tones['neutral'] }}">
                            @svg('lucide-' . ($lucideMap[$action['icon']] ?? $action['icon']), 'h-5 w-5')
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $action['label'] }}</p>
                            <p class="text-xs text-muted-foreground truncate">{{ $action['helper'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 3. ABOUT ============ --}}
    @if($aboutPage)
        @php
            $aboutBlocks = $aboutPage['blocks'] ?? [];
            $aboutHero = collect($aboutBlocks)->firstWhere('type', 'hero');
            $aboutRich = collect($aboutBlocks)->firstWhere('type', 'richText');
            $aboutTitle = $aboutHero['data']['title'] ?? 'World-Class Healthcare, Compassionately Delivered';
            $aboutHtml = $aboutRich['data']['html'] ?? '';
            $aboutPoints = $aboutContent['points'] ?? [];
        @endphp
        <section class="container-page section-y">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <div class="aspect-[5/4] rounded-3xl overflow-hidden hairline">
                        <img src="https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=1400&q=80" alt="Hospital exterior" class="size-full object-cover" loading="lazy" />
                    </div>
                    @if($aboutContent)
                        <div class="absolute -bottom-6 -right-6 hidden md:block bg-surface rounded-2xl shadow-elevated hairline p-6 max-w-[220px]">
                            <p class="text-3xl font-bold text-primary">{{ $aboutContent['stat_value'] ?? '25+' }}</p>
                            <p class="text-sm text-muted-foreground mt-1">{{ $aboutContent['stat_label'] ?? 'Years of clinical excellence' }}</p>
                        </div>
                    @endif
                </div>
                <div>
                    <p class="text-eyebrow mb-3">{{ $aboutContent['eyebrow'] ?? 'About' }} {{ $aboutPage['title'] }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-balance">{{ $aboutTitle }}</h2>
                    @if($aboutHtml)
                        <div class="mt-5 text-muted-foreground leading-relaxed text-pretty [&>p]:mt-3 first:[&>p]:mt-0">{!! $aboutHtml !!}</div>
                    @endif
                    @if(!empty($aboutPoints))
                        <ul class="mt-6 space-y-3">
                            @foreach($aboutPoints as $point)
                                <li class="flex items-start gap-3 text-sm">
                                    <svg class="h-4 w-4 text-secondary mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <a href="/pages/about-us" wire:navigate class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-primary hover:gap-3 transition-all">
                        {{ $aboutContent['learn_more_label'] ?? 'Learn more about us' }} <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 4. STATISTICS ============ --}}
    @if(!empty($stats))
        <section class="bg-primary text-primary-foreground">
            <div class="container-page py-14 md:py-16 grid grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($stats as $stat)
                    <div class="text-center md:text-left">
                        <p class="text-4xl md:text-5xl font-bold tracking-tight">{{ $stat['value'] }}</p>
                        <p class="mt-2 text-xs md:text-sm uppercase tracking-widest text-primary-foreground/70">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 5. SERVICES ============ --}}
    @if(!empty($services))
        @php $servicesAction = '<a href="' . route('services.index') . '" class="text-sm font-semibold text-primary hover:underline inline-flex items-center gap-2">View all services <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>'; @endphp
        <section class="container-page section-y">
            <x-ui.section-header
                eyebrow="{{ $homeSections['services_eyebrow'] ?? 'Services' }}"
                title="{{ $homeSections['services_title'] ?? 'Comprehensive care, end to end' }}"
                subtitle="{{ $homeSections['services_subtitle'] ?? 'From emergency response to long-term rehabilitation, every service is delivered by specialists who collaborate as one team.' }}"
                :action="$servicesAction"
            />
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($services as $service)
                    <a href="{{ route('services.show', $service['slug']) }}" wire:navigate class="group rounded-2xl bg-surface hairline p-6 hover:shadow-card hover:-translate-y-0.5 transition-all">
                        <div class="size-11 rounded-xl bg-primary-soft text-primary grid place-items-center mb-5">
                            @svg('lucide-' . ($lucideMap[$service['icon'] ?? ''] ?? $service['icon'] ?? 'stethoscope'), 'h-5 w-5')
                        </div>
                        <h3 class="text-lg font-semibold mb-2 group-hover:text-primary transition-colors">{{ $service['name'] }}</h3>
                        <p class="text-sm text-muted-foreground leading-relaxed">{{ $service['summary'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 6. DEPARTMENTS ============ --}}
    @if(!empty($departments))
        <section class="bg-surface-muted section-y">
            <div class="container-page">
@php $departmentsAction = '<a href="' . route('departments.index') . '" class="text-sm font-semibold text-primary hover:underline inline-flex items-center gap-2">All departments <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>'; @endphp
                <x-ui.section-header
                    eyebrow="{{ $homeSections['departments_eyebrow'] ?? 'Centers of Excellence' }}"
                    title="{{ $homeSections['departments_title'] ?? 'Specialized care across 40+ medical fields' }}"
                    :action="$departmentsAction"
                />
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach($departments as $dept)
                        <a href="{{ route('departments.show', $dept['slug']) }}" wire:navigate class="group flex flex-col items-center text-center rounded-2xl bg-surface hairline p-5 md:p-6 hover:bg-primary hover:text-primary-foreground transition-colors min-h-[140px] justify-center">
                            <div class="size-12 rounded-full bg-primary-soft text-primary grid place-items-center mb-3 group-hover:bg-white/10 group-hover:text-primary-foreground transition-colors">
                                @svg('lucide-' . ($lucideMap[$dept['icon'] ?? ''] ?? $dept['icon'] ?? 'building-2'), 'h-5 w-5')
                            </div>
                            <span class="text-sm font-semibold leading-tight">{{ $dept['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 7. DOCTORS ============ --}}
    @if(!empty($doctors))
        <section class="container-page section-y">@php $doctorsAction = '<a href="' . route('doctors.index') . '" class="text-sm font-semibold text-primary hover:underline inline-flex items-center gap-2">Find a doctor <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>'; @endphp
                <x-ui.section-header
                    eyebrow="{{ $homeSections['doctors_eyebrow'] ?? 'Our Specialists' }}"
                    title="{{ $homeSections['doctors_title'] ?? 'Meet the doctors setting new standards' }}"
                    subtitle="{{ $homeSections['doctors_subtitle'] ?? 'Internationally trained physicians with deep specialty experience and a shared commitment to compassionate care.' }}"
                    :action="$doctorsAction"
            />
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($doctors as $doc)
                    <x-sections.doctor-card
                        :photo="$doc['photo']"
                        :name="$doc['name']"
                        :href="route('doctors.show', $doc['slug'])"
                        :designation="$doc['designation']"
                        :meta="$doc['experience_years'] . '+ yrs · ' . $doc['department']"
                    />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 8. HEALTH PACKAGES ============ --}}
    @if(!empty($packages))
        <section class="bg-surface-muted section-y">
            <div class="container-page">
                <x-ui.section-header
                    eyebrow="{{ $homeSections['packages_eyebrow'] ?? 'Preventative Care' }}"
                    title="{{ $homeSections['packages_title'] ?? 'Health packages built around you' }}"
                    subtitle="{{ $homeSections['packages_subtitle'] ?? 'Same-day comprehensive screenings with results, lifestyle recommendations, and follow-up plans delivered by your care team.' }}"
                />
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($packages as $pkg)
                        <div class="relative rounded-2xl bg-surface p-7 flex flex-col {{ ($pkg['is_popular'] ?? false) ? 'hairline ring-2 ring-primary' : 'hairline' }}">
                            @if($pkg['is_popular'] ?? false)
                                <div class="absolute top-0 right-6 -translate-y-1/2 bg-primary text-primary-foreground text-[10px] tracking-widest uppercase font-bold px-3 py-1 rounded">Popular</div>
                            @endif
                            <p class="text-eyebrow text-secondary">{{ $pkg['tier'] }}</p>
                            <h3 class="text-lg font-semibold mt-2">{{ $pkg['name'] }}</h3>
                            <p class="text-sm text-muted-foreground mt-2 leading-relaxed flex-1">{{ $pkg['description'] }}</p>
                            <p class="mt-5 text-3xl font-bold">
                                ${{ number_format($pkg['price'], 0) }}
                                <span class="text-sm font-normal text-muted-foreground ml-1">/ package</span>
                                @if($pkg['original_price'] ?? false)
                                    <span class="ml-2 text-lg text-muted-foreground line-through">${{ number_format($pkg['original_price'], 0) }}</span>
                                @endif
                            </p>
                            <ul class="mt-5 space-y-2 text-sm text-foreground/80">
                                @foreach(array_slice($pkg['inclusions'] ?? [], 0, 3) as $inc)
                                    <li class="flex gap-2"><span class="text-secondary">✓</span> {{ $inc }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('health-packages') }}" wire:navigate class="mt-6 inline-flex items-center justify-center rounded-md py-2.5 text-sm font-semibold transition-colors {{ ($pkg['is_popular'] ?? false) ? 'bg-primary text-primary-foreground hover:opacity-90' : 'bg-muted text-foreground hover:bg-accent' }}">
                                View details
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 9. TREATMENTS ============ --}}
    @if(!empty($treatments))
        <section class="container-page section-y">
            <x-ui.section-header
                eyebrow="{{ $homeSections['treatments_eyebrow'] ?? 'Featured Treatments' }}"
                title="{{ $homeSections['treatments_title'] ?? 'Advanced procedures, refined outcomes' }}"
            />
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($treatments as $treatment)
                    <x-ui.media-card aspect="4/3" :src="$treatment['image']" :alt="$treatment['name']">
                        <div class="p-5">
                            <h3 class="font-semibold">{{ $treatment['name'] }}</h3>
                            <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $treatment['summary'] }}</p>
                        </div>
                    </x-ui.media-card>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 10. TECHNOLOGY ============ --}}
    @if(!empty($technologies))
        <section class="bg-surface-muted section-y">
            <div class="container-page">
                <x-ui.section-header
                    eyebrow="{{ $homeSections['technology_eyebrow'] ?? 'Medical Technology' }}"
                    title="{{ $homeSections['technology_title'] ?? 'Investments that change outcomes' }}"
                    subtitle="{{ $homeSections['technology_subtitle'] ?? 'From robotic surgery suites to image-guided radiation therapy, our platforms are chosen by clinicians for the outcomes they enable.' }}"
                />
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($technologies as $tech)
                        <div class="rounded-2xl bg-surface hairline p-6">
                            <div class="size-11 rounded-xl bg-secondary-soft text-secondary grid place-items-center mb-4">
                                @svg('lucide-' . ($lucideMap[$tech['icon'] ?? ''] ?? $tech['icon'] ?? 'cpu'), 'h-5 w-5')
                            </div>
                            <h3 class="font-semibold">{{ $tech['name'] }}</h3>
                            <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $tech['summary'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 11. WHY CHOOSE US ============ --}}
    @if($whyChooseUsPage)
        @php
            $whyBlocks = $whyChooseUsPage['blocks'] ?? [];
            $featuresBlock = collect($whyBlocks)->firstWhere('type', 'features');
            $features = $featuresBlock['data']['items'] ?? [];
        @endphp
        <section class="container-page section-y">
            <x-ui.section-header eyebrow="{{ $homeSections['why_choose_eyebrow'] ?? 'Why Shubham International' }}" title="{{ $homeSections['why_choose_title'] ?? 'The difference is in the details' }}" align="center" />
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-border rounded-2xl overflow-hidden hairline">
                @foreach($features as $idx => $item)
                    <div class="bg-surface p-8">
                        <p class="text-secondary font-bold text-2xl mb-3">0{{ $idx + 1 }}</p>
                        <h3 class="font-semibold mb-2">{{ $item['title'] }}</h3>
                        <p class="text-sm text-muted-foreground leading-relaxed">{{ $item['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 12. TESTIMONIALS ============ --}}
    @if(!empty($testimonials))
        <section class="bg-primary text-primary-foreground section-y">
            <div class="container-page">
                <x-ui.section-header
                    eyebrow="{{ $homeSections['testimonials_eyebrow'] ?? 'Patient Voices' }}"
                    title="{{ $homeSections['testimonials_title'] ?? 'Heard in their own words' }}"
                    align="center"
                    class="[&_.text-eyebrow]:text-secondary [&_h2]:text-primary-foreground [&_p]:text-primary-foreground/70"
                />
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($testimonials as $testimonial)
                        <figure class="rounded-2xl bg-white/5 hairline border-white/10 p-6 flex flex-col">
                            <svg class="h-6 w-6 text-secondary mb-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                            <blockquote class="text-sm leading-relaxed flex-1">"{{ $testimonial['quote'] }}"</blockquote>
                            @if($testimonial['rating'])
                                <div class="mt-5 flex items-center gap-1 text-secondary">
                                    @for($i = 0; $i < min(5, $testimonial['rating']); $i++)
                                        <svg class="h-3.5 w-3.5 fill-current" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    @endfor
                                </div>
                            @endif
                            <figcaption class="mt-3 text-sm">
                                <p class="font-semibold">{{ $testimonial['name'] }}</p>
                                <p class="text-xs text-primary-foreground/60">{{ $testimonial['location'] }} &middot; {{ $testimonial['treatment'] }}</p>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 13. PATIENT STORIES ============ --}}
    @if(!empty($stories))
        <section class="container-page section-y">
            <x-ui.section-header eyebrow="{{ $homeSections['stories_eyebrow'] ?? 'Patient Stories' }}" title="{{ $homeSections['stories_title'] ?? 'Real journeys, real outcomes' }}" />
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($stories as $story)
                    <a href="{{ $story['url'] }}" class="group rounded-2xl overflow-hidden hairline bg-surface">
                        <div class="aspect-[4/3] overflow-hidden bg-muted">
                            <img src="{{ $story['image'] }}" alt="{{ $story['title'] }}" loading="lazy" class="size-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        </div>
                        <div class="p-6">
                            <p class="text-xs text-secondary font-semibold uppercase tracking-widest">{{ $story['patient'] }}</p>
                            <h3 class="font-semibold mt-2 group-hover:text-primary transition-colors">{{ $story['title'] }}</h3>
                            <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $story['excerpt'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 14. INSURANCE PARTNERS ============ --}}
    @if(!empty($insurance))
        <section class="bg-surface-muted section-y">
            <div class="container-page">
                <x-ui.section-header eyebrow="{{ $homeSections['insurance_eyebrow'] ?? 'Insurance & TPA' }}" title="{{ $homeSections['insurance_title'] ?? 'Cashless treatment with leading insurers' }}" align="center" />
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($insurance as $partner)
                        <div class="aspect-[5/2] rounded-xl bg-surface hairline grid place-items-center text-sm font-semibold text-muted-foreground">
                            {{ $partner['name'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 15. AWARDS ============ --}}
    @if(!empty($awards))
        <section class="container-page section-y">
            <x-ui.section-header eyebrow="{{ $homeSections['awards_eyebrow'] ?? 'Recognition' }}" title="{{ $homeSections['awards_title'] ?? 'Awards & accreditation' }}" align="center" />
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($awards as $award)
                    <div class="rounded-2xl bg-surface hairline p-6 text-center">
                        <div class="size-12 rounded-full bg-secondary-soft text-secondary grid place-items-center mx-auto mb-4">
                            @svg('lucide-' . ($lucideMap[$award['icon'] ?? ''] ?? $award['icon'] ?? 'award'), 'h-5 w-5')
                        </div>
                        <h3 class="font-semibold text-sm">{{ $award['title'] }}</h3>
                        <p class="text-xs text-muted-foreground mt-2">{{ $award['issuer'] }} &middot; {{ $award['year'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 16. BLOGS ============ --}}
    @if(!empty($blogs))
        <section class="bg-surface-muted section-y">
            <div class="container-page">
@php $blogsAction = '<a href="' . route('blogs.index') . '" class="text-sm font-semibold text-primary hover:underline inline-flex items-center gap-2">All articles <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>'; @endphp
                <x-ui.section-header
                    eyebrow="{{ $homeSections['blogs_eyebrow'] ?? 'Health Library' }}"
                    title="{{ $homeSections['blogs_title'] ?? 'Latest from our doctors' }}"
                    :action="$blogsAction"
                />
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($blogs as $post)
                        <a href="{{ route('blogs.show', $post['slug']) }}" wire:navigate class="group rounded-2xl overflow-hidden bg-surface hairline">
                            <div class="aspect-[16/10] overflow-hidden bg-muted">
                                <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" loading="lazy" class="size-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                    <span class="text-secondary font-semibold">{{ $post['category'] }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $post['read_minutes'] }} min read</span>
                                </div>
                                <h3 class="font-semibold mt-3 group-hover:text-primary transition-colors text-balance">{{ $post['title'] }}</h3>
                                <p class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ $post['excerpt'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 17. CAREER ============ --}}
    @if($careerPage)
        @php
            $careerBlocks = $careerPage['blocks'] ?? [];
            $careerHero = collect($careerBlocks)->firstWhere('type', 'hero');
            $careerRich = collect($careerBlocks)->firstWhere('type', 'richText');
            $careerTitle = $careerHero['data']['title'] ?? 'Build your career where care comes first.';
            $careerSubtitle = $careerHero['data']['subtitle'] ?? '';
            $careerHtml = $careerRich['data']['html'] ?? '';
            $careerStatsList = $careerStatsContent['stats'] ?? [];
        @endphp
        <section class="container-page section-y">
            <div class="rounded-3xl bg-primary text-primary-foreground p-8 md:p-14 grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="text-eyebrow text-secondary mb-3">{{ $careerStatsContent['eyebrow'] ?? 'Careers' }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-balance">{{ $careerTitle }}</h2>
                    @if($careerSubtitle)
                        <p class="mt-4 text-primary-foreground/70 max-w-lg leading-relaxed">{{ $careerSubtitle }}</p>
                    @endif
                    @if($careerHtml)
                        <div class="mt-4 text-primary-foreground/70 max-w-lg leading-relaxed [&>p]:mt-2 first:[&>p]:mt-0">{!! $careerHtml !!}</div>
                    @endif
                    <a href="/pages/careers" class="mt-6 inline-flex items-center gap-2 rounded-md bg-surface px-6 py-3 text-sm font-semibold text-foreground hover:bg-secondary hover:text-secondary-foreground transition-colors">
                        {{ $careerStatsContent['openings_label'] ?? 'Explore openings' }} <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
                @if(!empty($careerStatsList))
                    @php $cols = min(count($careerStatsList), 4); @endphp
                    <div class="grid grid-cols-3 gap-4 text-center">
                        @foreach($careerStatsList as $statItem)
                            <div class="rounded-2xl bg-white/5 hairline border-white/10 p-5">
                                <p class="text-2xl md:text-3xl font-bold">{{ $statItem['value'] }}</p>
                                <p class="text-xs uppercase tracking-widest text-primary-foreground/60 mt-1">{{ $statItem['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ============ 18. FAQ ============ --}}
    @if(!empty($faqs))
        <section class="container-page section-y" x-data="{ open: {{ $faqs[0]['id'] ?? 'null' }} }">
            <div class="grid lg:grid-cols-12 gap-12">
                <div class="lg:col-span-4">
                    <p class="text-eyebrow mb-3">{{ $homeSections['faq_eyebrow'] ?? 'Patient FAQs' }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-balance">{{ $homeSections['faq_title'] ?? 'Answers to the questions we hear most.' }}</h2>
                    <p class="mt-4 text-muted-foreground leading-relaxed">{{ $homeSections['faq_subtitle'] ?? "Can't find what you're looking for? Our patient liaison team is available 24/7." }}</p>
                </div>
                <div class="lg:col-span-8 divide-y divide-border">
                    @foreach($faqs as $faq)
                        <div class="py-2">
                            <button type="button" class="w-full flex items-center justify-between gap-4 py-4 text-left" @click="open = open === {{ $faq['id'] }} ? null : {{ $faq['id'] }}" :aria-expanded="open === {{ $faq['id'] }}">
                                <span class="font-semibold pr-4">{{ $faq['question'] }}</span>
                                <template x-if="open === {{ $faq['id'] }}">
                                    <svg class="h-5 w-5 shrink-0 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                                </template>
                                <template x-if="open !== {{ $faq['id'] }}">
                                    <svg class="h-5 w-5 shrink-0 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                </template>
                            </button>
                            <div x-show="open === {{ $faq['id'] }}" x-collapse>
                                <p class="pb-5 text-sm text-muted-foreground leading-relaxed pr-10">{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ 19. CONTACT CTA ============ --}}
    <section class="container-page pb-20">
        <div class="rounded-3xl bg-gradient-to-br from-secondary to-primary text-primary-foreground p-8 md:p-12 text-center">
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-balance">{{ $homeSections['contact_cta_title'] ?? 'Ready to plan your visit?' }}</h2>
            <p class="mt-3 text-primary-foreground/80 max-w-xl mx-auto">
                {{ $homeSections['contact_cta_subtitle'] ?? 'Speak with a patient liaison at' }} <strong>{{ $settings['emergency_phone'] ?? '1-800-123-4567' }}</strong> {{ $homeSections['contact_cta_middle'] ?? 'or book an appointment online in under two minutes.' }}
            </p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('appointment') }}" wire:navigate class="inline-flex items-center gap-2 rounded-md bg-surface px-6 py-3 text-sm font-semibold text-foreground hover:bg-background transition-colors">
                    {{ $homeSections['contact_cta_book_label'] ?? 'Book Appointment' }} <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
                <a href="{{ route('contact') }}" wire:navigate class="inline-flex items-center rounded-md bg-white/10 border border-white/20 px-6 py-3 text-sm font-semibold text-primary-foreground hover:bg-white/20 transition-colors">
                    {{ $homeSections['contact_cta_contact_label'] ?? 'Contact us' }}
                </a>
            </div>
        </div>
    </section>
</div>
