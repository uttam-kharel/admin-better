<?php

namespace App\Livewire\Pages;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Service;
use Livewire\Component;

class DepartmentsShow extends Component
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

        return view('pages.departments.show', [
            'department' => $department,
            'doctors' => $doctors,
            'services' => $services,
        ]);
    }
}
