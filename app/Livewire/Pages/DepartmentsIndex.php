<?php

namespace App\Livewire\Pages;

use App\Models\Department;
use App\Support\PublicCache;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class DepartmentsIndex extends Component
{
    public function render()
    {
        $departments = Cache::remember(PublicCache::DEPARTMENTS, PublicCache::TTL, fn () => Department::all());

        return view('pages.departments.index', [
            'departments' => $departments,
        ]);
    }
}
