<?php

use App\Models\Appointment;
use App\Models\BlogPost;
use App\Models\CmsPage;
use App\Models\ContactSubmission;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\HealthPackage;
use App\Models\HeroSlide;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Service;
use Livewire\Component;
use App\Models\AdminUser;


new class extends Component
{
public array $tiles = [];
    public $recentAppointments;
    public $recentContacts;
    public $recentApplicants;

    public function mount(): void
    {
        $counts = [
            'doctors' => Doctor::count(),
            'departments' => Department::count(),
            'services' => Service::count(),
            'blogs' => BlogPost::count(),
            'pages' => CmsPage::count(),
            'adminUsers' => AdminUser::count(),
            'appointments' => Appointment::count(),
            'contactSubmissions' => ContactSubmission::count(),
            'heroSlides' => HeroSlide::count(),
            'healthPackages' => HealthPackage::count(),
            'jobOpenings' => JobOpening::count(),
            'jobApplications' => JobApplication::count(),
        ];

        $this->tiles = [
            ['url' => route('admin.doctors'), 'icon' => 'lucide-stethoscope', 'count' => $counts['doctors'], 'label' => 'Doctors'],
            ['url' => route('admin.departments'), 'icon' => 'lucide-building-2', 'count' => $counts['departments'], 'label' => 'Departments'],
            ['url' => route('admin.services'), 'icon' => 'lucide-list-checks', 'count' => $counts['services'], 'label' => 'Services'],
            ['url' => route('admin.blogs'), 'icon' => 'lucide-newspaper', 'count' => $counts['blogs'], 'label' => 'Blogs'],
            ['url' => route('admin.job-openings'), 'icon' => 'lucide-briefcase', 'count' => $counts['jobOpenings'], 'label' => 'Job Openings'],
            ['url' => route('admin.job-applications'), 'icon' => 'lucide-file-text', 'count' => $counts['jobApplications'], 'label' => 'Applications'],
            ['url' => route('admin.appointments'), 'icon' => 'lucide-calendar-check', 'count' => $counts['appointments'], 'label' => 'Appointments'],
            ['url' => route('admin.contact-submissions'), 'icon' => 'lucide-inbox', 'count' => $counts['contactSubmissions'], 'label' => 'Contact Inbox'],
        ];

        $this->recentAppointments = Appointment::latest()->take(5)->get();
        $this->recentContacts = ContactSubmission::latest()->take(5)->get();
        $this->recentApplicants = JobApplication::with('jobOpening')->latest()->take(5)->get();
    }

    public function render()
    {
        return $this->view()
            ->layout('layouts.admin', ['title' => 'Dashboard — Admin']);
    }
};

?>
<div>
    <div class="space-y-8">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Overview</h2>
            <p class="text-sm text-muted-foreground mt-1">All site content and operations at a glance.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            @foreach($tiles as $tile)
                <x-ui.stat-card :href="$tile['url']" :value="$tile['count']" :label="$tile['label']">
                    <x-slot:icon>@svg($tile['icon'])</x-slot:icon>
                </x-ui.stat-card>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-3 gap-4">
            <x-ui.card padding="none" class="overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold text-sm">Recent appointments</h3>
                    <a href="{{ route('admin.appointments') }}" class="text-xs text-primary font-semibold inline-flex items-center gap-1">
                        View all <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
                @if($recentAppointments->count() === 0)
                    <p class="p-6 text-sm text-muted-foreground">No appointments yet.</p>
                @else
                    <ul class="divide-y divide-border">
                        @foreach($recentAppointments as $apt)
                            <li class="px-5 py-3 flex items-center justify-between gap-3 text-sm">
                                <div class="min-w-0">
                                    <p class="font-medium truncate">{{ $apt->name }}</p>
                                    <p class="text-xs text-muted-foreground truncate">{{ $apt->department_slug }} &middot; {{ $apt->preferred_date }}</p>
                                </div>
                                <span class="text-[10px] uppercase tracking-widest font-semibold px-2 py-0.5 rounded bg-muted">{{ $apt->status }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>

            <x-ui.card padding="none" class="overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold text-sm">Recent applicants</h3>
                    <a href="{{ route('admin.job-applications') }}" class="text-xs text-primary font-semibold inline-flex items-center gap-1">
                        View all <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
                @if($recentApplicants->count() === 0)
                    <p class="p-6 text-sm text-muted-foreground">No applications yet.</p>
                @else
                    <ul class="divide-y divide-border">
                        @foreach($recentApplicants as $app)
                            <li class="px-5 py-3 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-medium truncate">{{ $app->name }}</p>
                                    <span class="text-[10px] uppercase tracking-widest font-semibold px-2 py-0.5 rounded bg-muted">{{ $app->status }}</span>
                                </div>
                                <p class="text-xs text-muted-foreground truncate mt-0.5">{{ $app->jobOpening?->title }} &middot; {{ $app->created_at->format('M j') }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>

            <x-ui.card padding="none" class="overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold text-sm">Recent contact messages</h3>
                    <a href="{{ route('admin.contact-submissions') }}" class="text-xs text-primary font-semibold inline-flex items-center gap-1">
                        View all <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
                @if($recentContacts->count() === 0)
                    <p class="p-6 text-sm text-muted-foreground">No messages yet.</p>
                @else
                    <ul class="divide-y divide-border">
                        @foreach($recentContacts as $msg)
                            <li class="px-5 py-3 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-medium truncate">{{ $msg->name }}</p>
                                    <span class="text-[10px] uppercase tracking-widest font-semibold px-2 py-0.5 rounded bg-muted">{{ $msg->status ?? 'unread' }}</span>
                                </div>
                                <p class="text-xs text-muted-foreground line-clamp-1 mt-0.5">{{ $msg->message }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>
    </div>
</div>
