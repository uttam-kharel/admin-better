<?php

namespace App\Livewire\Pages;

use App\Models\{
    HeroSlide,
    QuickAction,
    Stat,
    Service,
    Department,
    Doctor,
    HealthPackage,
    Treatment,
    Technology,
    Testimonial,
    PatientStory,
    InsurancePartner,
    Award,
    BlogPost,
    Faq,
    CmsPage,
    SiteSetting
};
use App\Support\PublicCache;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class HomepageIndex extends Component
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
        // All homepage content is static — cache it as one blob (refreshed on any
        // admin save/delete, with a 1h backstop). Cuts ~17 queries per homepage load.
        $data = Cache::remember(PublicCache::HOMEPAGE, PublicCache::TTL, function () {
            $pages = [];
            foreach (['about-us', 'why-choose-us', 'careers'] as $slug) {
                $page = CmsPage::where('slug', $slug)->first();
                if ($page) {
                    $pages[$slug] = $page->toArray();
                }
            }

            return [
                'heroSlides' => HeroSlide::orderBy('order')->get()->toArray(),
                'quickActions' => QuickAction::all()->toArray(),
                'stats' => Stat::all()->toArray(),
                'services' => Service::all()->toArray(),
                'departments' => Department::all()->toArray(),
                'doctors' => Doctor::take(4)->get()->toArray(),
                'packages' => HealthPackage::all()->toArray(),
                'treatments' => Treatment::all()->toArray(),
                'technologies' => Technology::all()->toArray(),
                'testimonials' => Testimonial::all()->toArray(),
                'stories' => PatientStory::all()->toArray(),
                'insurance' => InsurancePartner::all()->toArray(),
                'awards' => Award::all()->toArray(),
                'blogs' => BlogPost::latest()->take(3)->get()->toArray(),
                'faqs' => Faq::all()->toArray(),
                'pages' => $pages,
            ];
        });

        $this->heroSlides = $data['heroSlides'];
        $this->quickActions = $data['quickActions'];
        $this->stats = $data['stats'];
        $this->services = $data['services'];
        $this->departments = $data['departments'];
        $this->doctors = $data['doctors'];
        $this->packages = $data['packages'];
        $this->treatments = $data['treatments'];
        $this->technologies = $data['technologies'];
        $this->testimonials = $data['testimonials'];
        $this->stories = $data['stories'];
        $this->insurance = $data['insurance'];
        $this->awards = $data['awards'];
        $this->blogs = $data['blogs'];
        $this->faqs = $data['faqs'];
        $this->aboutPage = $data['pages']['about-us'] ?? null;
        $this->whyChooseUsPage = $data['pages']['why-choose-us'] ?? null;
        $this->careerPage = $data['pages']['careers'] ?? null;

        $this->siteSetting = SiteSetting::cached();
        $this->settings = $this->siteSetting?->toArray();
        $this->homeSections = $this->siteSetting?->home_sections ?? [];
        $this->aboutContent = $this->siteSetting?->about ?? [];
        $this->careerStatsContent = $this->siteSetting?->career_stats ?? [];
        $this->heroContent = $this->siteSetting?->hero ?? [];
    }

    public function render()
    {
        $siteName = $this->settings['site_name'] ?? 'Shubham International';

        return view('pages.homepage.index')
            ->layout('layouts.public', ['title' => $siteName . ' — Advanced Medical Care for Every Generation']);
    }
}
