<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;

class GalleryItemSeeder extends Seeder
{
    public function run(): void
    {
        GalleryItem::insert([
            ['id' => 1, 'type' => 'photo', 'title' => 'Main lobby', 'url' => 'https://images.unsplash.com/photo-1551076805-e1869033e561?auto=format&fit=crop&w=1600&q=80', 'thumbnail' => 'https://images.unsplash.com/photo-1551076805-e1869033e561?auto=format&fit=crop&w=600&q=70', 'category' => 'Facility'],
            ['id' => 2, 'type' => 'photo', 'title' => 'Cardiac cath lab', 'url' => 'https://images.unsplash.com/photo-1631815589968-fdb09a223b1e?auto=format&fit=crop&w=1600&q=80', 'thumbnail' => 'https://images.unsplash.com/photo-1631815589968-fdb09a223b1e?auto=format&fit=crop&w=600&q=70', 'category' => 'Departments'],
            ['id' => 3, 'type' => 'photo', 'title' => 'Pediatric ward', 'url' => 'https://images.unsplash.com/photo-1581056771107-24ca5f033842?auto=format&fit=crop&w=1600&q=80', 'thumbnail' => 'https://images.unsplash.com/photo-1581056771107-24ca5f033842?auto=format&fit=crop&w=600&q=70', 'category' => 'Departments'],
            ['id' => 4, 'type' => 'photo', 'title' => 'Operation theatre', 'url' => 'https://images.unsplash.com/photo-1559757175-5700dde675bc?auto=format&fit=crop&w=1600&q=80', 'thumbnail' => 'https://images.unsplash.com/photo-1559757175-5700dde675bc?auto=format&fit=crop&w=600&q=70', 'category' => 'Facility'],
            ['id' => 5, 'type' => 'photo', 'title' => 'Diagnostic imaging', 'url' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1600&q=80', 'thumbnail' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=600&q=70', 'category' => 'Departments'],
            ['id' => 6, 'type' => 'tour', 'title' => 'Virtual hospital tour', 'url' => 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=1600&q=80', 'thumbnail' => 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=600&q=70', 'category' => 'Tour'],
        ]);
    }
}
