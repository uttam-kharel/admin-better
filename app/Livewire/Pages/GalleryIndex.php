<?php

namespace App\Livewire\Pages;

use App\Models\GalleryItem;
use Livewire\Component;

class GalleryIndex extends Component
{
    public string $selectedCategory = '';

    public function render()
    {
        $items = GalleryItem::all();
        $categories = $items->pluck('category')->unique();

        return view('pages.gallery.index', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }
}
