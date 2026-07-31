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

        return view('pages.homepage.index')
            ->layout('layouts.public', ['title' => $siteName . ' — Advanced Medical Care for Every Generation']);
    }
}
