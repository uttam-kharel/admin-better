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
                <x-slot:icon>@svg('lucide-phone')</x-slot:icon>
                <a href="tel:{{ $contact['patient_helpline'] ?? ($settings?->primary_phone ?? '18001234567') }}" class="text-primary font-semibold text-lg">{{ $contact['patient_helpline'] ?? '1-800-123-4567' }}</a>
            </x-ui.panel>
            <x-ui.panel :label="$contact['emergency_label'] ?? 'Emergency'">
                <x-slot:icon>@svg('lucide-phone')</x-slot:icon>
                <a href="tel:{{ $contact['emergency_phone'] ?? ($settings?->emergency_phone ?? '18009999999') }}" class="text-emergency font-semibold text-lg">{{ $contact['emergency_phone'] ?? '1-800-999-9999' }}</a>
            </x-ui.panel>
            <x-ui.panel :label="$contact['email_label'] ?? 'Email'">
                <x-slot:icon>@svg('lucide-mail')</x-slot:icon>
                <a href="mailto:{{ $contact['email'] ?? ($settings?->email ?? 'care@lumina.health') }}" class="font-semibold">{{ $contact['email'] ?? ($settings?->email ?? 'care@lumina.health') }}</a>
            </x-ui.panel>
            <x-ui.panel :label="$contact['opd_label'] ?? 'OPD hours'">
                <x-slot:icon>@svg('lucide-clock')</x-slot:icon>
                <p class="text-sm">{{ $contact['opd_hours'] ?? 'Mon–Sat · 8 AM – 8 PM' }}</p>
            </x-ui.panel>
            <x-ui.panel :label="$contact['location_label'] ?? 'Main hospital'" class="sm:col-span-2">
                <x-slot:icon>@svg('lucide-map-pin')</x-slot:icon>
                <p class="text-sm">{{ $contact['address'] ?? ($settings?->address ?? '1500 Medical Plaza, New York, NY 10001') }}</p>
            </x-ui.panel>
        </div>
        <div class="rounded-2xl overflow-hidden hairline bg-muted aspect-[4/3] grid place-items-center text-muted-foreground text-sm">
            {{ $contact['map_placeholder'] ?? 'Interactive map placeholder' }}
        </div>
    </section>
</div>
