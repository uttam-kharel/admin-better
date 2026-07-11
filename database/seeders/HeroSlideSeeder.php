<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        HeroSlide::insert([
            ['id' => 1, 'eyebrow' => 'World-class healthcare', 'title' => 'Advanced medical care for every generation', 'subtitle' => 'Combining specialized expertise with compassionate care to deliver world-class medical outcomes across our network of excellence.', 'image' => 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=1600&q=80', 'cta_label' => 'Book Appointment', 'cta_url' => '/appointment', 'secondary_cta_label' => 'Find a Specialist', 'secondary_cta_url' => '/doctors', 'order' => 0],
            ['id' => 2, 'eyebrow' => 'Now open', 'title' => 'Robot-assisted neurosurgery wing', 'subtitle' => 'Our newest center brings sub-millimeter precision to complex brain and spine procedures, supported by an interdisciplinary team.', 'image' => 'https://images.unsplash.com/photo-1631815589968-fdb09a223b1e?auto=format&fit=crop&w=1600&q=80', 'cta_label' => 'Explore the Center', 'cta_url' => '/departments/neurology', 'secondary_cta_label' => 'Meet the Team', 'secondary_cta_url' => '/doctors', 'order' => 1],
            ['id' => 3, 'eyebrow' => 'Preventative care', 'title' => 'Executive health screenings, redesigned', 'subtitle' => 'Comprehensive same-day check-ups, advanced imaging, and lifestyle plans tailored to busy professionals.', 'image' => 'https://images.unsplash.com/photo-1551076805-e1869033e561?auto=format&fit=crop&w=1600&q=80', 'cta_label' => 'Browse Packages', 'cta_url' => '/health-packages', 'secondary_cta_label' => null, 'secondary_cta_url' => null, 'order' => 2],
        ]);
    }
}
