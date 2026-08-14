<?php

namespace App\Livewire\Pages;

use App\Models\Service;
use App\Support\PublicCache;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ServicesIndex extends Component
{
    public function render()
    {
        $services = Cache::remember(PublicCache::SERVICES, PublicCache::TTL, fn () => Service::all());

        return view('pages.services.index', [
            'services' => $services,
        ]);
    }
}
