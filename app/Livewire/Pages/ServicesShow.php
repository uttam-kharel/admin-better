<?php

namespace App\Livewire\Pages;

use App\Models\Service;
use Livewire\Component;

class ServicesShow extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $service = Service::where('slug', $this->slug)->firstOrFail();
        $related = Service::where('id', '!=', $service->id)->take(3)->get();

        return view('pages.services.show', [
            'service' => $service,
            'related' => $related,
        ]);
    }
}
