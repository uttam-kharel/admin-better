<?php

use Livewire\Component;
use App\Models\Doctor;


new class extends Component
{
public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $doctor = Doctor::where('slug', $this->slug)->firstOrFail();
        $related = Doctor::where('department_slug', $doctor->department_slug)
            ->where('id', '!=', $doctor->id)
            ->take(3)
            ->get();

        return $this->view(['doctor' => $doctor, 'related' => $related]);
    }
};

?>
<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-10 md:py-14">
            <a href="{{ route('doctors.index') }}" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-primary mb-6">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                All doctors
            </a>
            <div class="grid lg:grid-cols-[280px_1fr] gap-8 md:gap-12">
                <div>
                    <div class="aspect-[4/5] rounded-2xl overflow-hidden hairline bg-muted">
                        <img src="{{ $doctor->photo }}" alt="{{ $doctor->name }}" class="size-full object-cover" />
                    </div>
                </div>
                <div>
                    <p class="text-eyebrow mb-2">{{ $doctor->department }}</p>
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight">{{ $doctor->name }}</h1>
                    <p class="mt-2 text-lg text-secondary font-medium">{{ $doctor->designation }}</p>
                    <p class="mt-5 text-muted-foreground leading-relaxed max-w-2xl">{{ $doctor->bio }}</p>

                    <div class="mt-8 grid sm:grid-cols-2 gap-5 max-w-2xl">
                        <div class="flex items-start gap-3">
                            <div class="size-9 rounded-full bg-primary-soft text-primary grid place-items-center shrink-0">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 8 2 12 2s5 2 6.5 2a2.5 2.5 0 0 1 0 5H18"/><path d="M18 10v9"/><path d="M6 10v9"/><path d="M6 15h12"/><path d="M4 19h16"/></svg>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-muted-foreground">Experience</p>
                                <p class="text-sm font-medium">{{ $doctor->experience_years }}+ years</p>
                            </div>
                        </div>
                        @if($doctor->languages)
                            <div class="flex items-start gap-3">
                                <div class="size-9 rounded-full bg-primary-soft text-primary grid place-items-center shrink-0">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-muted-foreground">Languages</p>
                                    <p class="text-sm font-medium">{{ is_array($doctor->languages) ? implode(', ', $doctor->languages) : $doctor->languages }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('appointment') }}?doctor={{ $doctor->slug }}" class="inline-flex items-center rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground">
                            Book appointment
                        </a>
                        @if($doctor->department_slug)
                            <a href="{{ route('departments.show', $doctor->department_slug) }}" class="inline-flex items-center rounded-md hairline bg-surface px-5 py-3 text-sm font-semibold">
                                View department
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container-page py-12 grid lg:grid-cols-3 gap-8">
        @if($doctor->qualifications)
            <div class="rounded-2xl bg-surface hairline p-6">
                <h3 class="flex items-center gap-2 font-semibold mb-4 text-sm uppercase tracking-widest text-primary">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    Qualifications
                </h3>
                <ul class="space-y-2 text-sm text-muted-foreground">
                    @foreach($doctor->qualifications as $q)
                        <li>&middot; {{ $q }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($doctor->expertise)
            <div class="rounded-2xl bg-surface hairline p-6">
                <h3 class="flex items-center gap-2 font-semibold mb-4 text-sm uppercase tracking-widest text-primary">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 8 2 12 2s5 2 6.5 2a2.5 2.5 0 0 1 0 5H18"/><path d="M18 10v9"/><path d="M6 10v9"/><path d="M6 15h12"/><path d="M4 19h16"/></svg>
                    Areas of expertise
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($doctor->expertise as $exp)
                        <span class="rounded-full bg-secondary-soft text-secondary text-xs font-medium px-3 py-1.5">{{ $exp }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($doctor->schedule)
            <div class="rounded-2xl bg-surface hairline p-6">
                <h3 class="flex items-center gap-2 font-semibold mb-4 text-sm uppercase tracking-widest text-primary">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    Consultation schedule
                </h3>
                <ul class="space-y-3 text-sm">
                    @foreach($doctor->schedule as $s)
                        @php $day = is_array($s) ? $s['day'] : $s->day; $hours = is_array($s) ? $s['hours'] : $s->hours; @endphp
                        <li class="flex justify-between gap-3">
                            <span class="font-medium">{{ $day }}</span>
                            <span class="text-muted-foreground">{{ $hours }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    @if($doctor->publications)
        <section class="container-page py-8">
            <h2 class="text-2xl font-bold mb-4">Publications</h2>
            <ul class="space-y-3 text-sm text-muted-foreground max-w-3xl">
                @foreach($doctor->publications as $p)
                    <li>&middot; {{ $p }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($related->count() > 0)
        <section class="container-page py-12 mt-8">
            <h2 class="text-2xl font-bold mb-6">Related specialists</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($related as $rel)
                    <x-sections.related-doctor
                        :photo="$rel->photo"
                        :name="$rel->name"
                        :href="route('doctors.show', $rel->slug)"
                        :designation="$rel->designation"
                    />
                @endforeach
            </div>
        </section>
    @endif
</div>
