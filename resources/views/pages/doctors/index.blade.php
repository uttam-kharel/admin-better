<?php

use App\Models\Doctor;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{

    public string $search = '';
    public string $departmentSlug = '';
    public int $perPage = 12;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDepartmentSlug(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Doctor::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('designation', 'like', "%{$this->search}%")
                  ->orWhere('department', 'like', "%{$this->search}%");
            });
        }

        if ($this->departmentSlug) {
            $query->where('department_slug', $this->departmentSlug);
        }

        $doctors = $query->paginate($this->perPage);
        $departments = Department::all();

        return $this->view([
            'doctors' => $doctors,
            'departments' => $departments,
        ])->layout('layouts.public', ['title' => 'Our Doctors — Shubham International Hospital']);
    }
};

?>

<div>
    <x-sections.page-hero eyebrow="Find a Doctor" title="Meet our specialists" subtitle="Search by name or browse by department to find the right physician for your care.">
        <div class="mt-8 grid sm:grid-cols-[1fr_240px] gap-3 max-w-3xl">
            <x-form.search-input
                wire:model.live.debounce="search"
                placeholder="Search by name, expertise…"
                aria-label="Search doctors"
            />
            <select
                wire:model.live="departmentSlug"
                class="rounded-md bg-surface hairline px-3 py-3 text-sm"
                aria-label="Browse department"
            >
                <option value="">All departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->slug }}">{{ $d->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            @foreach($departments->take(6) as $d)
                <x-ui.pill variant="filled" :active="$departmentSlug === $d->slug" :href="route('departments.show', $d->slug)">
                    {{ $d->name }}
                    <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>
                </x-ui.pill>
            @endforeach
            @if($departments->count() > 6)
                <x-ui.pill variant="filled" :href="route('departments.index')">
                    View all departments
                </x-ui.pill>
            @endif
        </div>
    </x-sections.page-hero>

    <section class="container-page py-12">
        <p class="text-sm text-muted-foreground mb-6">{{ $doctors->total() }} doctor{{ $doctors->total() !== 1 ? 's' : '' }}</p>
        @if($doctors->count() === 0)
            <x-feedback.empty-state title="No doctors match your search." />
        @else
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($doctors as $doc)
                    <x-sections.doctor-card
                        :photo="$doc->photo"
                        :name="$doc->name"
                        :href="route('doctors.show', $doc->slug)"
                        :designation="$doc->designation"
                        :meta="$doc->experience_years . '+ yrs · ' . $doc->department"
                    />
                @endforeach
            </div>
        @endif
    </section>
</div>
