<?php

namespace App\Livewire\Pages;

use App\Models\Appointment as AppointmentModel;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\SiteSetting;
use Livewire\Component;

class AppointmentIndex extends Component
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

        return view('pages.appointment.index', [
            'departments' => $departments,
            'doctors' => $doctors,
            'sidebar' => $sidebar,
            'siteSetting' => $siteSetting,
        ]);
    }
}
