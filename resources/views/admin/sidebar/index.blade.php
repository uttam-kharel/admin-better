<?php

use Livewire\Component;

new class extends Component
{
public function render()
    {
        return $this->view();
    }
};

?>
<div class="hidden lg:flex w-64 shrink-0 border-r border-border bg-surface flex-col h-dvh overflow-y-auto">
    <div class="px-5 py-5 border-b border-border">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-2.5">
            <div class="size-9 rounded-lg bg-primary text-primary-foreground grid place-items-center font-bold">S</div>
            <div>
                <div class="text-sm font-bold leading-tight">Shubham Admin</div>
                <div class="text-[10px] text-muted-foreground tracking-widest uppercase">Control Panel</div>
            </div>
        </a>
    </div>
    <nav class="px-3 py-4 space-y-5">
        @php
        $groups = [
            [
                'label' => 'Overview',
                'items' => [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
                    ['route' => 'admin.analytics', 'label' => 'Analytics', 'icon' => 'bar-chart-3'],
                ],
            ],
            [
                'label' => 'Operations',
                'items' => [
                    ['route' => 'admin.appointments', 'label' => 'Appointments', 'icon' => 'calendar-check'],
                    ['route' => 'admin.contact-submissions', 'label' => 'Contact Inbox', 'icon' => 'inbox'],
                    ['route' => 'admin.job-applications', 'label' => 'Job Applications', 'icon' => 'file-text'],
                ],
            ],
            [
                'label' => 'Core Content',
                'items' => [
                    ['route' => 'admin.doctors', 'label' => 'Doctors', 'icon' => 'stethoscope'],
                    ['route' => 'admin.departments', 'label' => 'Departments', 'icon' => 'building-2'],
                    ['route' => 'admin.services', 'label' => 'Services', 'icon' => 'list-checks'],
                    ['route' => 'admin.health-packages', 'label' => 'Health Packages', 'icon' => 'package'],
                    ['route' => 'admin.job-openings', 'label' => 'Job Openings', 'icon' => 'briefcase'],
                    ['route' => 'admin.blogs', 'label' => 'Blogs', 'icon' => 'newspaper'],
                    ['route' => 'admin.authors', 'label' => 'Authors', 'icon' => 'users'],
                    ['route' => 'admin.gallery', 'label' => 'Gallery', 'icon' => 'images'],
                ],
            ],
            [
                'label' => 'Marketing',
                'items' => [
                    ['route' => 'admin.hero-slides', 'label' => 'Hero Slides', 'icon' => 'image'],
                    ['route' => 'admin.quick-actions', 'label' => 'Quick Actions', 'icon' => 'activity'],
                    ['route' => 'admin.stats', 'label' => 'Stats', 'icon' => 'activity'],
                    ['route' => 'admin.testimonials', 'label' => 'Testimonials', 'icon' => 'message-square-quote'],
                    ['route' => 'admin.stories', 'label' => 'Patient Stories', 'icon' => 'heart'],
                    ['route' => 'admin.treatments', 'label' => 'Treatments', 'icon' => 'heart'],
                    ['route' => 'admin.technologies', 'label' => 'Technologies', 'icon' => 'cpu'],
                    ['route' => 'admin.awards', 'label' => 'Awards', 'icon' => 'award'],
                    ['route' => 'admin.insurance', 'label' => 'Insurance', 'icon' => 'shield-check'],
                    ['route' => 'admin.faqs', 'label' => 'FAQs', 'icon' => 'help-circle'],
                ],
            ],
            [
                'label' => 'Site Structure',
                'items' => [
                    ['route' => 'admin.menus', 'label' => 'Menus', 'icon' => 'menu'],
                    ['route' => 'admin.pages', 'label' => 'Pages (CMS)', 'icon' => 'file-text'],
                    ['route' => 'admin.settings', 'label' => 'Site Settings', 'icon' => 'settings'],
                ],
            ],
            [
                'label' => 'System',
                'items' => [
                    ['route' => 'admin.admin-users', 'label' => 'Admin Users', 'icon' => 'users'],
                ],
            ],
        ];
        $currentRoute = request()->route()?->getName();
        @endphp

        @foreach($groups as $group)
            <div>
                <p class="px-3 mb-1.5 text-[10px] font-bold tracking-widest uppercase text-muted-foreground">{{ $group['label'] }}</p>
                <div class="space-y-0.5">
                    @foreach($group['items'] as $item)
                        @php $isActive = $currentRoute === $item['route']; @endphp
                        <a href="{{ route($item['route']) }}" wire:navigate
                           class="flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors {{ $isActive ? 'bg-primary text-primary-foreground font-semibold' : 'text-foreground/70 hover:bg-muted hover:text-foreground' }}">
                            @svg('lucide-' . $item['icon'], 'h-4 w-4 shrink-0')
                            <span class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>
</div>
