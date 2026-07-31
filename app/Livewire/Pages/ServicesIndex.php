<?php

namespace App\Livewire\Pages;

use App\Models\Service;
use Livewire\Component;

class ServicesIndex extends Component
{
    public function render()
    {
        $services = Service::all();

        return view('pages.services.index', [
            'services' => $services,
        ]);
    }
}
