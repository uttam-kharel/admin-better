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
            @svg('lucide-alert-triangle', 'h-3.5 w-3.5 text-emergency shrink-0')
            <span class="hidden sm:inline truncate opacity-80">{{ $topbar['emergency_text'] ?? '24/7 Emergency Response' }} &middot; {{ $topbar['trauma_text'] ?? 'Level-1 Trauma Center' }}</span>
            <span class="sm:hidden truncate opacity-80">{{ $topbar['emergency_text'] ?? '24/7 Emergency' }}</span>
        </div>
        <div class="flex items-center gap-3 sm:gap-5">
            <a href="tel:{{ $topbar['phone'] ?? ($settings?->primary_phone ?? '18001234567') }}" class="hidden sm:flex items-center gap-1.5 hover:text-secondary transition-colors">
                @svg('lucide-phone', 'h-3.5 w-3.5') {{ $topbar['phone'] ?? ($settings?->primary_phone ?? '1-800-123-4567') }}
            </a>
            <a href="{{ $topbar['patient_portal_url'] ?? '/pages/patient-portal' }}" wire:navigate class="hover:text-secondary transition-colors hidden md:inline">{{ $topbar['patient_portal_label'] ?? 'Patient Portal' }}</a>
            <a href="tel:{{ $topbar['phone'] ?? ($settings?->emergency_phone ?? '18001234567') }}" class="bg-emergency text-emergency-foreground px-3 py-1 rounded-sm font-semibold tracking-wide hover:opacity-90 transition-opacity">Emergency</a>
        </div>
    </div>
</div>
