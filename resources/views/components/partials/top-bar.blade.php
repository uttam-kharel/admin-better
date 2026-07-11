<?php

use Livewire\Component;
use App\Models\SiteSetting;


new class extends Component
{
public function render()
    {
        $settings = SiteSetting::first();
        $topbar = $settings?->topbar ?? [];

        return $this->view([
            'topbar' => $topbar,
            'settings' => $settings,
        ]);
    }
};

?>
<div class="bg-foreground text-background text-xs">
    <div class="container-page flex h-9 items-center justify-between gap-4">
        <div class="flex items-center gap-2 min-w-0">
            <svg class="h-3.5 w-3.5 text-emergency shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
            <span class="hidden sm:inline truncate opacity-80">{{ $topbar['emergency_text'] ?? '24/7 Emergency Response' }} &middot; {{ $topbar['trauma_text'] ?? 'Level-1 Trauma Center' }}</span>
            <span class="sm:hidden truncate opacity-80">{{ $topbar['emergency_text'] ?? '24/7 Emergency' }}</span>
        </div>
        <div class="flex items-center gap-3 sm:gap-5">
            <a href="tel:{{ $topbar['phone'] ?? ($settings?->primary_phone ?? '18001234567') }}" class="hidden sm:flex items-center gap-1.5 hover:text-secondary transition-colors">
                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg> {{ $topbar['phone'] ?? ($settings?->primary_phone ?? '1-800-123-4567') }}
            </a>
            <a href="{{ $topbar['patient_portal_url'] ?? '/pages/patient-portal' }}" wire:navigate class="hover:text-secondary transition-colors hidden md:inline">{{ $topbar['patient_portal_label'] ?? 'Patient Portal' }}</a>
            <a href="tel:{{ $topbar['phone'] ?? ($settings?->emergency_phone ?? '18001234567') }}" class="bg-emergency text-emergency-foreground px-3 py-1 rounded-sm font-semibold tracking-wide hover:opacity-90 transition-opacity">Emergency</a>
        </div>
    </div>
</div>
