<?php

use Livewire\Component;
use App\Models\SiteSetting;


new class extends Component
{
public function render()
    {
        $settings = SiteSetting::first();
        $contact = $settings?->contact_page ?? [];
        $siteName = $settings?->site_name ?? 'Shubham International';

        return $this->view([
            'contact' => $contact,
            'settings' => $settings,
        ]);
    }
};

?>
<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-12 md:py-16">
            <p class="text-eyebrow mb-3">{{ $contact['eyebrow'] ?? 'Contact' }}</p>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $contact['title'] ?? "We're here for you, 24/7" }}</h1>
        </div>
    </section>

    <section class="container-page py-12 grid lg:grid-cols-2 gap-10">
        <div class="grid sm:grid-cols-2 gap-4 content-start">
            <x-ui.panel :label="$contact['patient_helpline_label'] ?? 'Patient helpline'">
                <x-slot:icon><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></x-slot:icon>
                <a href="tel:{{ $contact['patient_helpline'] ?? ($settings?->primary_phone ?? '18001234567') }}" class="text-primary font-semibold text-lg">{{ $contact['patient_helpline'] ?? '1-800-123-4567' }}</a>
            </x-ui.panel>
            <x-ui.panel :label="$contact['emergency_label'] ?? 'Emergency'">
                <x-slot:icon><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></x-slot:icon>
                <a href="tel:{{ $contact['emergency_phone'] ?? ($settings?->emergency_phone ?? '18009999999') }}" class="text-emergency font-semibold text-lg">{{ $contact['emergency_phone'] ?? '1-800-999-9999' }}</a>
            </x-ui.panel>
            <x-ui.panel :label="$contact['email_label'] ?? 'Email'">
                <x-slot:icon><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></x-slot:icon>
                <a href="mailto:{{ $contact['email'] ?? ($settings?->email ?? 'care@lumina.health') }}" class="font-semibold">{{ $contact['email'] ?? ($settings?->email ?? 'care@lumina.health') }}</a>
            </x-ui.panel>
            <x-ui.panel :label="$contact['opd_label'] ?? 'OPD hours'">
                <x-slot:icon><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></x-slot:icon>
                <p class="text-sm">{{ $contact['opd_hours'] ?? 'Mon–Sat · 8 AM – 8 PM' }}</p>
            </x-ui.panel>
            <x-ui.panel :label="$contact['location_label'] ?? 'Main hospital'" class="sm:col-span-2">
                <x-slot:icon><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></x-slot:icon>
                <p class="text-sm">{{ $contact['address'] ?? ($settings?->address ?? '1500 Medical Plaza, New York, NY 10001') }}</p>
            </x-ui.panel>
        </div>
        <div class="rounded-2xl overflow-hidden hairline bg-muted aspect-[4/3] grid place-items-center text-muted-foreground text-sm">
            {{ $contact['map_placeholder'] ?? 'Interactive map placeholder' }}
        </div>
    </section>
</div>
