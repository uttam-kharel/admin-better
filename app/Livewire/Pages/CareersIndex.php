<?php

namespace App\Livewire\Pages;

use App\Models\JobOpening;
use App\Models\SiteSetting;
use Livewire\Component;
use Livewire\WithPagination;

class CareersIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $category = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = JobOpening::available()->orderBy('order');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%")
                  ->orWhere('department', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $jobs = $query->paginate(12);

        $categoryCounts = JobOpening::available()
            ->selectRaw('category, count(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category');

        $categories = $categoryCounts->keys()->mapWithKeys(fn ($key) => [
            $key => ucwords(str_replace(['-', '_'], ' ', $key)),
        ])->toArray();

        $types = JobOpening::available()
            ->select('type')
            ->distinct()
            ->whereNotNull('type')
            ->orderBy('type')
            ->pluck('type')
            ->toArray();

        $departments = JobOpening::available()
            ->select('department')
            ->distinct()
            ->whereNotNull('department')
            ->orderBy('department')
            ->pluck('department');

        $siteSetting = SiteSetting::first();
        $careersContent = $siteSetting?->careers_page ?? [];

        return view('pages.careers.index', [
            'jobs' => $jobs,
            'categories' => $categories,
            'categoryCounts' => $categoryCounts,
            'types' => $types,
            'departments' => $departments,
            'careersContent' => $careersContent,
        ]);
    }
}
