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
use App\Models\PageVisit;
use App\Models\Service;
use Livewire\Component;
use App\Models\AdminUser;


new class extends Component
{    public array $tiles = [];
    public $recentAppointments;
    public $recentContacts;
    public $recentApplicants;

    public array $traffic = [];
    public $chartDays;
    public $topPages;
    public $deviceSplit;
    public $browserSplit;
    public $recentVisits;

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

        $this->traffic = [
            'total' => PageVisit::count(),
            'unique' => PageVisit::query()->distinct()->count('visitor_id'),
            'today' => PageVisit::whereDate('created_at', today())->count(),
            'week' => PageVisit::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Last 14 days, zero-filled so the chart always has 14 bars.
        $raw = PageVisit::query()
            ->selectRaw("date(created_at) as day, count(*) as total")
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->pluck('total', 'day');

        $chart = collect();
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $chart->push(['day' => $day, 'total' => (int) ($raw[$day] ?? 0)]);
        }
        $this->chartDays = $chart;

        $this->topPages = PageVisit::query()
            ->selectRaw('path, count(*) as total')
            ->groupBy('path')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $this->deviceSplit = PageVisit::query()
            ->selectRaw('COALESCE(device, "unknown") as device, count(*) as total')
            ->groupBy('device')
            ->orderByDesc('total')
            ->get();

        $this->browserSplit = PageVisit::query()
            ->selectRaw('COALESCE(browser, "Other") as browser, count(*) as total')
            ->groupBy('browser')
            ->orderByDesc('total')
            ->get();

        $this->recentVisits = PageVisit::latest()->take(6)->get();
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

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <x-ui.stat-card :value="number_format($traffic['total'])" label="Total visits">
                <x-slot:icon>@svg('lucide-eye')</x-slot:icon>
            </x-ui.stat-card>
            <x-ui.stat-card :value="number_format($traffic['unique'])" label="Unique visitors">
                <x-slot:icon>@svg('lucide-users')</x-slot:icon>
            </x-ui.stat-card>
            <x-ui.stat-card :value="number_format($traffic['today'])" label="Visits today">
                <x-slot:icon>@svg('lucide-calendar-days')</x-slot:icon>
            </x-ui.stat-card>
            <x-ui.stat-card :value="number_format($traffic['week'])" label="Visits (7 days)">
                <x-slot:icon>@svg('lucide-trending-up')</x-slot:icon>
            </x-ui.stat-card>
        </div>

        <div class="grid lg:grid-cols-3 gap-4">
            <x-ui.card class="lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-sm">Traffic — last 14 days</h3>
                        <p class="text-xs text-muted-foreground mt-0.5">Daily page visits</p>
                    </div>
                    <a href="{{ route('admin.analytics') }}" class="text-xs text-primary font-semibold inline-flex items-center gap-1">
                        View analytics @svg('lucide-arrow-right', 'h-3 w-3')
                    </a>
                </div>
                <div
                    x-data="{
                        labels: @js($chartDays->pluck('day')->map(fn ($b) => \Carbon\Carbon::parse($b)->format('d M'))->values()),
                        values: @js($chartDays->pluck('total')->values()),
                        init() { AdminCharts.renderLineChart(this.$refs.canvas, this.labels, this.values); },
                        destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
                    }"
                    wire:key="dash-traffic-chart"
                >
                    <div class="h-56">
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card padding="none" class="overflow-hidden">
                <div class="px-5 py-4 border-b border-border">
                    <h3 class="font-semibold text-sm">Top pages</h3>
                </div>
                @if($topPages->count() === 0)
                    <p class="p-6 text-sm text-muted-foreground">No visits recorded yet.</p>
                @else
                    <ul class="divide-y divide-border">
                        @foreach($topPages as $page)
                            <li class="px-5 py-3 flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium truncate">{{ $page->path }}</span>
                                <span class="text-xs text-muted-foreground tabular-nums shrink-0">{{ $page->total }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>

        <div class="grid lg:grid-cols-3 gap-4">
            <x-ui.card padding="none" class="overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold text-sm">Recent appointments</h3>
                    <a href="{{ route('admin.appointments') }}" class="text-xs text-primary font-semibold inline-flex items-center gap-1">
                        View all @svg('lucide-arrow-right', 'h-3 w-3')
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
                        View all @svg('lucide-arrow-right', 'h-3 w-3')
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
                <div class="px-5 py-4 border-b border-border">
                    <h3 class="font-semibold text-sm">Devices</h3>
                </div>
                <div class="px-5 py-4">
                    <div
                    x-data="{
                        labels: @js($deviceSplit->pluck('device')->map(fn ($d) => ucfirst($d))->values()),
                        values: @js($deviceSplit->pluck('total')->values()),
                        init() { AdminCharts.renderDoughnut(this.$refs.canvas, this.labels, this.values); },
                        destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
                    }"
                        wire:key="dash-devices-chart"
                    >
                        <div class="h-44">
                            <canvas x-ref="canvas"></canvas>
                        </div>
                    </div>
                    <div class="pt-3 mt-3 border-t border-border space-y-1.5">
                        @foreach($browserSplit->take(4) as $browser)
                            <div class="flex justify-between text-xs">
                                <span class="text-muted-foreground">{{ $browser->browser }}</span>
                                <span class="tabular-nums">{{ $browser->total }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card padding="none" class="overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="font-semibold text-sm">Recent contact messages</h3>
                    <a href="{{ route('admin.contact-submissions') }}" class="text-xs text-primary font-semibold inline-flex items-center gap-1">
                        View all @svg('lucide-arrow-right', 'h-3 w-3')
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
        <div class="grid lg:grid-cols-3 gap-4">
            <x-ui.card padding="none" class="overflow-hidden lg:col-span-3">
                <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-sm">Recent visits</h3>
                        <p class="text-xs text-muted-foreground mt-0.5">Latest page views across the site</p>
                    </div>
                    <a href="{{ route('admin.analytics') }}" class="text-xs text-primary font-semibold inline-flex items-center gap-1">
                        View all @svg('lucide-arrow-right', 'h-3 w-3')
                    </a>
                </div>
                @if($recentVisits->count() === 0)
                    <p class="p-6 text-sm text-muted-foreground">No visits recorded yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[10px] uppercase tracking-widest text-muted-foreground border-b border-border">
                                    <th class="px-5 py-2.5 font-semibold">Page</th>
                                    <th class="px-5 py-2.5 font-semibold hidden md:table-cell">Referrer</th>
                                    <th class="px-5 py-2.5 font-semibold hidden sm:table-cell">Device</th>
                                    <th class="px-5 py-2.5 font-semibold hidden sm:table-cell">Browser</th>
                                    <th class="px-5 py-2.5 font-semibold">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($recentVisits as $visit)
                                    <tr>
                                        <td class="px-5 py-3 font-medium truncate max-w-[220px]">{{ $visit->path }}</td>
                                        <td class="px-5 py-3 text-xs text-muted-foreground truncate max-w-[180px] hidden md:table-cell">
                                            @if($visit->referer)
                                                {{ parse_url($visit->referer, PHP_URL_HOST) }}
                                            @else
                                                <span class="text-muted-foreground/50">Direct</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-xs text-muted-foreground capitalize hidden sm:table-cell">{{ $visit->device ?? '—' }}</td>
                                        <td class="px-5 py-3 text-xs text-muted-foreground hidden sm:table-cell">{{ $visit->browser ?? '—' }}</td>
                                        <td class="px-5 py-3 text-xs text-muted-foreground tabular-nums">{{ $visit->created_at->format('M j, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</div>
