<?php

namespace App\Livewire\Pages;

use App\Models\Department;
use Livewire\Component;

class DepartmentsIndex extends Component
{
    public function render()
    {
        $departments = Department::all();

        return view('pages.departments.index', [
            'departments' => $departments,
        ]);
    }
}
