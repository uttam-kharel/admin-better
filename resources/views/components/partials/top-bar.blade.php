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
<div class="sticky top-0 z-[60] bg-emergency text-emergency-foreground text-xs shadow-[0_2px_10px_rgba(185,28,28,0.25)]">
    <div class="container-page flex h-10 items-center justify-center sm:justify-between gap-4">
        <div class="flex items-center gap-2 min-w-0">
            <span class="emg-pulse" aria-hidden="true"></span>
            <span aria-hidden="true">@svg('lucide-ambulance', 'h-4 w-4 shrink-0')</span>
            <span class="truncate font-semibold">24/7 Emergency:</span>
            <a href="tel:{{ $topbar['phone'] ?? ($settings?->emergency_phone ?? '18001234567') }}" class="truncate font-bold underline underline-offset-2 hover:opacity-85 transition-opacity">
                {{ $topbar['phone'] ?? ($settings?->emergency_phone ?? '+977-1-XXXXXXX') }}
            </a>
        </div>
        <div class="hidden md:flex items-center gap-5">
            <a href="tel:{{ $topbar['phone'] ?? ($settings?->primary_phone ?? '18001234567') }}" class="hidden sm:flex items-center gap-1.5 opacity-90 hover:opacity-100 transition-opacity">
                @svg('lucide-phone', 'h-3.5 w-3.5') {{ $topbar['phone'] ?? ($settings?->primary_phone ?? '1-800-123-4567') }}
            </a>
            <a href="{{ $topbar['patient_portal_url'] ?? '/pages/patient-portal' }}" wire:navigate class="hidden lg:inline opacity-90 hover:opacity-100 transition-opacity">{{ $topbar['patient_portal_label'] ?? 'Patient Portal' }}</a>
        </div>
    </div>
</div>
