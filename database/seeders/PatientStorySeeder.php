<?php

namespace Database\Seeders;

use App\Models\PatientStory;
use Illuminate\Database\Seeder;

class PatientStorySeeder extends Seeder
{
    public function run(): void
    {
        PatientStory::insert([
            ['id' => 1, 'slug' => 'anita-heart-journey', 'title' => "Anita's heart journey", 'excerpt' => 'How a same-day intervention saved a marathon runner.', 'patient' => 'Anita Sharma', 'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?auto=format&fit=crop&w=1200&q=80', 'url' => '/pages/stories/anita-heart-journey'],
            ['id' => 2, 'slug' => 'klein-knee', 'title' => 'Back on the mountains at 62', 'excerpt' => 'A robot-assisted knee replacement gets Robert back to hiking.', 'patient' => 'Robert Klein', 'image' => 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=1200&q=80', 'url' => '/pages/stories/klein-knee'],
            ['id' => 3, 'slug' => 'gonzalez-cancer-care', 'title' => 'Beating the odds together', 'excerpt' => 'A coordinated cancer program across three continents.', 'patient' => 'Maria Gonzalez', 'image' => 'https://images.unsplash.com/photo-1582719471384-894fbb16e074?auto=format&fit=crop&w=1200&q=80', 'url' => '/pages/stories/gonzalez-cancer-care'],
        ]);
    }
}
