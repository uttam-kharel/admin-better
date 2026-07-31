<?php

namespace App\Livewire\Pages;

use App\Models\Doctor;
use Livewire\Component;

class DoctorsShow extends Component
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

        return view('pages.doctors.show', [
            'doctor' => $doctor,
            'related' => $related,
        ]);
    }
}
