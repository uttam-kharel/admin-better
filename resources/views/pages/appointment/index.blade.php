<?php

use App\Models\Appointment as AppointmentModel;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\SiteSetting;
use Livewire\Component;

new class extends Component
{
public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $departmentSlug = '';
    public string $doctorSlug = '';
    public string $preferredDate = '';
    public string $message = '';

    public bool $success = false;
    public string $appointmentId = '';
    public bool $submitting = false;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'departmentSlug' => 'required|string',
        'doctorSlug' => 'nullable|string',
        'preferredDate' => 'required|date|after:today',
        'message' => 'nullable|string|max:1000',
    ];

    public function submit(): void
    {
        $this->submitting = true;
        $this->validate();

        $id = 'APT-' . substr((string) time(), -6);
        AppointmentModel::create([
            'id' => $id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'department_slug' => $this->departmentSlug,
            'doctor_slug' => $this->doctorSlug ?: null,
            'preferred_date' => $this->preferredDate,
            'message' => $this->message ?: null,
            'status' => 'pending',
        ]);

        $this->success = true;
        $this->appointmentId = $id;
        $this->submitting = false;
        $this->reset(['name', 'email', 'phone', 'departmentSlug', 'doctorSlug', 'preferredDate', 'message']);
    }

    public function resetForm(): void
    {
        $this->success = false;
        $this->appointmentId = '';
    }

    public function render()
    {
        $departments = Department::all();
        $doctors = $this->departmentSlug ? Doctor::where('department_slug', $this->departmentSlug)->get() : collect();

        $siteSetting = SiteSetting::first();
        $sidebar = $siteSetting?->appointment_sidebar ?? [];
        $siteName = $siteSetting?->site_name ?? 'Shubham International';

        return $this->view([
            'departments' => $departments,
            'doctors' => $doctors,
            'sidebar' => $sidebar,
            'siteSetting' => $siteSetting,
        ]);
    }
};

?>
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
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
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
                        <svg wire:loading wire:target="submit" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        <span wire:loading.remove wire:target="submit">Request appointment</span>
                        <span wire:loading wire:target="submit">Submitting&hellip;</span>
                    </button>
                </form>
            </div>
        @endif

        <aside class="space-y-4">
            <x-ui.panel :label="$sidebar['call_label'] ?? 'Call us'">
                <x-slot:icon><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></x-slot:icon>
                <a href="tel:{{ $sidebar['helpline'] ?? ($siteSetting?->primary_phone ?? '18001234567') }}" class="text-primary font-semibold">{{ $sidebar['helpline'] ?? ($siteSetting?->primary_phone ?? '1-800-123-4567') }}</a>
                <p class="text-sm text-muted-foreground mt-1">{{ $sidebar['helpline_note'] ?? '24/7 patient helpline' }}</p>
            </x-ui.panel>
            <x-ui.panel :label="$sidebar['hours_label'] ?? 'OPD Hours'">
                <x-slot:icon><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></x-slot:icon>
                <p class="text-sm">{{ $sidebar['hours'] ?? 'Mon&ndash;Sat &middot; 8:00 AM &ndash; 8:00 PM' }}</p>
                <p class="text-sm text-muted-foreground mt-1">{{ $sidebar['emergency_note'] ?? 'Emergency 24/7' }}</p>
            </x-ui.panel>
            <x-ui.panel :label="$sidebar['location_label'] ?? 'Location'">
                <x-slot:icon><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></x-slot:icon>
                <p class="text-sm">{{ $sidebar['location'] ?? '1500 Medical Plaza' }}</p>
                <p class="text-sm text-muted-foreground">{{ $sidebar['location_city'] ?? 'New York, NY' }}</p>
            </x-ui.panel>
        </aside>
    </section>
</div>
