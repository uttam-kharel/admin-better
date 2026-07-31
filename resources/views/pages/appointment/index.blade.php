<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-12 md:py-16">
            <p class="text-eyebrow mb-3">{{ $sidebar['page_eyebrow'] ?? 'Book Appointment' }}</p>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $sidebar['page_title'] ?? 'Schedule your visit' }}</h1>
            <p class="mt-3 text-lg text-muted-foreground max-w-2xl">
                {{ $sidebar['page_subtitle'] ?? 'Complete the form and a care coordinator will confirm your slot within 30 minutes.' }}
            </p>
        </div>
    </section>

    <section class="container-page py-12 grid lg:grid-cols-[1fr_320px] gap-8">
        @if($success)
            <x-feedback.success-panel title="Appointment request received">
                <x-slot:icon>
                    @svg('lucide-badge-check', 'h-7 w-7')
                </x-slot:icon>
                <p class="text-muted-foreground mt-2 text-sm">
                    Your reference number is <span class="font-mono font-semibold text-foreground">{{ $appointmentId }}</span>.
                    A care coordinator will confirm your slot within 30 minutes.
                </p>
                <button type="button" wire:click="resetForm" class="mt-6 inline-flex items-center rounded-md bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground">
                    Book another appointment
                </button>
            </x-feedback.success-panel>
        @else
            <div>
                <form wire:submit="submit" class="rounded-2xl bg-surface hairline p-6 md:p-8 space-y-5" novalidate>
                    <div class="grid md:grid-cols-2 gap-5">
                        <label class="block">
                            <span class="block text-sm font-medium mb-1.5">Full name <span class="text-destructive">*</span></span>
                            <input type="text" wire:model="name" autocomplete="name" required class="block w-full rounded-md bg-surface border border-input px-3.5 py-2.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition @error('name') border-destructive @enderror" placeholder="Jane Doe" />
                            @error('name') <span class="block mt-1 text-xs text-destructive">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="block text-sm font-medium mb-1.5">Phone <span class="text-destructive">*</span></span>
                            <input type="tel" wire:model="phone" autocomplete="tel" required class="block w-full rounded-md bg-surface border border-input px-3.5 py-2.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition @error('phone') border-destructive @enderror" placeholder="+1 555 010 1234" />
                            @error('phone') <span class="block mt-1 text-xs text-destructive">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="block">
                        <span class="block text-sm font-medium mb-1.5">Email <span class="text-destructive">*</span></span>
                        <input type="email" wire:model="email" autocomplete="email" required class="block w-full rounded-md bg-surface border border-input px-3.5 py-2.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition @error('email') border-destructive @enderror" placeholder="you@example.com" />
                        @error('email') <span class="block mt-1 text-xs text-destructive">{{ $message }}</span> @enderror
                    </label>

                    <div class="grid md:grid-cols-2 gap-5">
                        <label class="block">
                            <span class="block text-sm font-medium mb-1.5">Department <span class="text-destructive">*</span></span>
                            <select wire:model.live="departmentSlug" required class="block w-full rounded-md bg-surface border border-input px-3.5 py-2.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition @error('departmentSlug') border-destructive @enderror">
                                <option value="">Select a department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->slug }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('departmentSlug') <span class="block mt-1 text-xs text-destructive">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="block text-sm font-medium mb-1.5">Preferred date <span class="text-destructive">*</span></span>
                            <input type="date" wire:model="preferredDate" required class="block w-full rounded-md bg-surface border border-input px-3.5 py-2.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition @error('preferredDate') border-destructive @enderror" />
                            @error('preferredDate') <span class="block mt-1 text-xs text-destructive">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="block">
                        <span class="block text-sm font-medium mb-1.5">Message (optional)</span>
                        <textarea wire:model="message" rows="4" class="block w-full rounded-md bg-surface border border-input px-3.5 py-2.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition resize-none @error('message') border-destructive @enderror" placeholder="Tell us briefly what you'd like to discuss"></textarea>
                        @error('message') <span class="block mt-1 text-xs text-destructive">{{ $message }}</span> @enderror
                    </label>

                    <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="inline-flex items-center justify-center gap-2 w-full md:w-auto rounded-md bg-primary px-7 py-3 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 disabled:opacity-60 transition-opacity">
                        @svg('lucide-refresh-cw', 'h-4 w-4 animate-spin', ['wire:loading' => true, 'wire:target' => 'submit'])
                        <span wire:loading.remove wire:target="submit">Request appointment</span>
                        <span wire:loading wire:target="submit">Submitting&hellip;</span>
                    </button>
                </form>
            </div>
        @endif

        <aside class="space-y-4">
            <x-ui.panel :label="$sidebar['call_label'] ?? 'Call us'">
                <x-slot:icon>@svg('lucide-phone')</x-slot:icon>
                <a href="tel:{{ $sidebar['helpline'] ?? ($siteSetting?->primary_phone ?? '18001234567') }}" class="text-primary font-semibold">{{ $sidebar['helpline'] ?? ($siteSetting?->primary_phone ?? '1-800-123-4567') }}</a>
                <p class="text-sm text-muted-foreground mt-1">{{ $sidebar['helpline_note'] ?? '24/7 patient helpline' }}</p>
            </x-ui.panel>
            <x-ui.panel :label="$sidebar['hours_label'] ?? 'OPD Hours'">
                <x-slot:icon>@svg('lucide-clock')</x-slot:icon>
                <p class="text-sm">{{ $sidebar['hours'] ?? 'Mon&ndash;Sat &middot; 8:00 AM &ndash; 8:00 PM' }}</p>
                <p class="text-sm text-muted-foreground mt-1">{{ $sidebar['emergency_note'] ?? 'Emergency 24/7' }}</p>
            </x-ui.panel>
            <x-ui.panel :label="$sidebar['location_label'] ?? 'Location'">
                <x-slot:icon>@svg('lucide-map-pin')</x-slot:icon>
                <p class="text-sm">{{ $sidebar['location'] ?? '1500 Medical Plaza' }}</p>
                <p class="text-sm text-muted-foreground">{{ $sidebar['location_city'] ?? 'New York, NY' }}</p>
            </x-ui.panel>
        </aside>
    </section>
</div>
