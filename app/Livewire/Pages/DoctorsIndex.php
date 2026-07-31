<?php

namespace App\Livewire\Pages;

use App\Models\Department;
use App\Models\Doctor;
use Livewire\Component;
use Livewire\WithPagination;

class DoctorsIndex extends Component
{
    use WithPagination;

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

        return view('pages.doctors.index', [
            'doctors' => $doctors,
            'departments' => $departments,
        ])->layout('layouts.public', ['title' => 'Our Doctors — Shubham International Hospital']);
    }
}
