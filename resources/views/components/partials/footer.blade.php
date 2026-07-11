<?php

use App\Models\SiteSetting;
use Livewire\Component;
use App\Models\MenuItem;


new class extends Component
{
public function render()
    {
        $patientMenu = MenuItem::with('children')->where('slug', 'patients')->first();
        $aboutMenu = MenuItem::with('children')->where('slug', 'about')->first();
        $wellnessMenu = MenuItem::with('children')->where('slug', 'wellness')->first();
        $settings = SiteSetting::first();

        return $this->view([
            'patientMenu' => $patientMenu,
            'aboutMenu' => $aboutMenu,
            'wellnessMenu' => $wellnessMenu,
            'settings' => $settings,
        ]);
    }
};

?>
<footer class="bg-foreground text-background mt-24">
    @php $footerContent = $settings?->footer ?? []; @endphp
    <div class="container-page py-16 md:py-20">
        <div class="grid lg:grid-cols-12 gap-10 pb-12 border-b border-white/10">
            <div class="lg:col-span-4">
                <div class="mb-5">
                    <x-ui.logo tone="secondary" size="md" :logo-text="$settings?->logo_text ?? 'S'" :label="$settings?->site_name ?? 'Shubham International'" />
                </div>
                <p class="text-sm text-background/60 max-w-sm leading-relaxed">
                    {{ $footerContent['tagline'] ?? 'Defining the standard of clinical excellence and patient-first medical care across our global network of multi-specialty hospitals.' }}
                </p>
                <div class="flex gap-3 mt-6">
                    @foreach(['facebook', 'twitter', 'instagram', 'linkedin'] as $social)
                        @if($settings && $settings->{$social})
                            <a href="{{ $settings->{$social} }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social }}" class="size-10 rounded-full border border-white/10 grid place-items-center hover:bg-white/5 transition-colors">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                </svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            @foreach([$patientMenu, $wellnessMenu, $aboutMenu] as $col)
                @if($col)
                    <div class="lg:col-span-2">
                        <h4 class="text-eyebrow text-secondary mb-5">{{ $col->title }}</h4>
                        <ul class="space-y-3 text-sm">
                            @foreach($col->children as $child)
                                <li><a href="{{ $child->url ?? '/' }}" wire:navigate class="text-background/70 hover:text-secondary transition-colors">{{ $child->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach

            <div class="lg:col-span-2">
                <h4 class="text-eyebrow text-secondary mb-5">Contact</h4>
                <ul class="space-y-3 text-sm text-background/70">
                    <li class="flex gap-3">
                        <svg class="h-4 w-4 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>{{ $settings?->primary_phone ?? '1-800-123-4567' }}</span>
                    </li>
                    <li class="flex gap-3">
                        <svg class="h-4 w-4 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <span>{{ $settings?->email ?? 'care@lumina.health' }}</span>
                    </li>
                    <li class="flex gap-3">
                        <svg class="h-4 w-4 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>{{ $settings?->address ?? '1500 Medical Plaza, New York, NY' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-xs text-background/40">
            <p>&copy; {{ date('Y') }} {{ $footerContent['copyright'] ?? 'Shubham International Hospital. All rights reserved.' }}</p>
            @if(!empty($footerContent['accreditations']))
                <div class="flex flex-wrap gap-x-6 gap-y-2 uppercase tracking-widest">
                    @foreach($footerContent['accreditations'] as $badge)
                        <span>{{ $badge }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</footer>
