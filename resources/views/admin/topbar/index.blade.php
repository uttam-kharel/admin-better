<?php

new class extends Component
{
public function render()
    {
        $title = $this->getTitle();
        return $this->view(['title' => $title]);
    }

    private function getTitle(): string
    {
        $route = request()->route()?->getName() ?? '';

        return match (true) {
            str_contains($route, 'dashboard') => 'Dashboard',
            str_contains($route, 'appointments') => 'Appointments',
            str_contains($route, 'contact-submissions') => 'Contact Inbox',
            str_contains($route, 'doctors') => 'Doctors',
            str_contains($route, 'departments') => 'Departments',
            str_contains($route, 'services') => 'Services',
            str_contains($route, 'health-packages') => 'Health Packages',
            str_contains($route, 'blogs') => 'Blogs',
            str_contains($route, 'authors') => 'Authors',
            str_contains($route, 'gallery') => 'Gallery',
            str_contains($route, 'hero-slides') => 'Hero Slides',
            str_contains($route, 'quick-actions') => 'Quick Actions',
            str_contains($route, 'stats') => 'Stats',
            str_contains($route, 'testimonials') => 'Testimonials',
            str_contains($route, 'stories') => 'Patient Stories',
            str_contains($route, 'treatments') => 'Treatments',
            str_contains($route, 'technologies') => 'Technologies',
            str_contains($route, 'awards') => 'Awards',
            str_contains($route, 'job-applications') => 'Job Applications',
            str_contains($route, 'insurance') => 'Insurance',
            str_contains($route, 'faqs') => 'FAQs',
            str_contains($route, 'menus') => 'Menus',
            str_contains($route, 'pages') => 'Pages',
            str_contains($route, 'settings') => 'Site Settings',
            str_contains($route, 'admin-users') => 'Admin Users',
            default => 'Dashboard',
        };
    }
};

?>
<header class="h-16 shrink-0 border-b border-border bg-background flex items-center px-4 lg:px-6 gap-3">
    <button type="button" class="lg:hidden p-2 rounded-md hover:bg-muted min-h-11 min-w-11 grid place-items-center" aria-label="Open sidebar" @click="$dispatch('toggle-admin-sidebar')">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
    </button>
    <div class="flex-1 min-w-0">
        <h1 class="text-lg font-semibold tracking-tight truncate">{{ $title }}</h1>
    </div>
    <a href="/" target="_blank" rel="noreferrer" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium text-muted-foreground hover:text-foreground px-3 py-1.5 rounded-md border border-border">
        View site <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
    </a>
    @auth('admin')
        <div class="flex items-center gap-3">
            <div class="hidden md:block text-right">
                <div class="text-xs font-semibold leading-tight">{{ auth('admin')->user()?->name }}</div>
                <div class="text-[10px] uppercase tracking-widest text-muted-foreground">{{ auth('admin')->user()?->role }}</div>
            </div>
            <div class="size-9 rounded-full bg-primary/10 text-primary grid place-items-center font-semibold text-sm">
                @php($adminName = auth('admin')->user()?->name ?? '')
                {{ collect(explode(' ', $adminName))->filter()->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('') }}
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                @csrf
                <button type="submit" class="p-2 rounded-md hover:bg-muted text-muted-foreground hover:text-foreground" aria-label="Log out" title="Log out">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                </button>
            </form>
        </div>
    @endauth
</header>
