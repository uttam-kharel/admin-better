<?php

use App\Models\Doctor;
use App\Models\Service;
use Livewire\Component;
use App\Models\Department;


new class extends Component
{
public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $department = Department::where('slug', $this->slug)->firstOrFail();
        $doctors = Doctor::where('department_slug', $this->slug)->get();
        $services = Service::where('department_slug', $this->slug)->get();

        return $this->view([
            'department' => $department,
            'doctors' => $doctors,
            'services' => $services,
        ]);
    }
};

?>
<div>
    <section class="bg-gradient-to-b from-primary-soft to-background">
        <div class="container-page py-10 md:py-14 grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <a href="{{ route('departments.index') }}" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-primary mb-6">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    All departments
                </a>
                <div class="flex items-center gap-3 mb-3">
                    <div class="size-10 rounded-xl bg-primary text-primary-foreground grid place-items-center">
                        @svg('lucide-' . ($lucideMap[$department->icon ?? ''] ?? $department->icon ?? 'building-2'), 'h-5 w-5')
                    </div>
                    <p class="text-eyebrow">{{ $department->tagline ?? 'Specialty' }}</p>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight">{{ $department->name }}</h1>
                <p class="mt-4 text-lg text-muted-foreground leading-relaxed">{{ $department->description }}</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('appointment') }}" class="inline-flex rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground">
                        Book appointment
                    </a>
                    <a href="{{ route('doctors.index') }}" class="inline-flex rounded-md hairline bg-surface px-5 py-3 text-sm font-semibold">
                        See doctors
                    </a>
                </div>
            </div>
            <div class="aspect-[5/4] rounded-3xl overflow-hidden hairline">
                <img src="{{ $department->image }}" alt="{{ $department->name }}" class="size-full object-cover" />
            </div>
        </div>
    </section>

    <section class="container-page py-12 grid lg:grid-cols-2 gap-8">
        @if($department->treatments)
            <div class="rounded-2xl bg-surface hairline p-7">
                <h2 class="text-xl font-bold mb-5">Treatments offered</h2>
                <ul class="grid sm:grid-cols-2 gap-3">
                    @foreach($department->treatments as $t)
                        <li class="flex items-start gap-2 text-sm">
                            <svg class="h-4 w-4 text-secondary shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            {{ $t }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($department->facilities)
            <div class="rounded-2xl bg-surface hairline p-7">
                <h2 class="text-xl font-bold mb-5">Facilities</h2>
                <ul class="grid sm:grid-cols-2 gap-3">
                    @foreach($department->facilities as $f)
                        <li class="flex items-start gap-2 text-sm">
                            <svg class="h-4 w-4 text-secondary shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            {{ $f }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    @if($doctors->count() > 0)
        <section class="container-page py-8">
            <h2 class="text-2xl font-bold mb-6">Our {{ $department->name }} specialists</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($doctors as $doc)
                    <x-sections.doctor-card
                        :photo="$doc->photo"
                        :name="$doc->name"
                        :href="route('doctors.show', $doc->slug)"
                        :designation="$doc->designation"
                    />
                @endforeach
            </div>
        </section>
    @endif
</div>
