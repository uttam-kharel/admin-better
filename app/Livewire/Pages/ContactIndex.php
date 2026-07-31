<?php

namespace App\Livewire\Pages;

use App\Models\SiteSetting;
use Livewire\Component;

class ContactIndex extends Component
{
    public function render()
    {
        $settings = SiteSetting::first();
        $contact = $settings?->contact_page ?? [];

        return view('pages.contact.index', [
            'contact' => $contact,
            'settings' => $settings,
        ]);
    }
}
