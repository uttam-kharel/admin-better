<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        Testimonial::insert([
            ['id' => 1, 'name' => 'Anita Sharma', 'location' => 'New Delhi, India', 'rating' => 5, 'quote' => 'From triage to discharge, the cardiology team made what felt impossible feel manageable.', 'treatment' => 'Coronary angioplasty', 'photo' => null],
            ['id' => 2, 'name' => 'Robert Klein', 'location' => 'Frankfurt, Germany', 'rating' => 5, 'quote' => "World-class care without the cold corporate feel. They explained every step.", 'treatment' => 'Knee replacement', 'photo' => null],
            ['id' => 3, 'name' => 'Maria Gonzalez', 'location' => 'Mexico City', 'rating' => 5, 'quote' => 'The international patient desk handled everything — visa, travel, follow-up. Exceptional.', 'treatment' => 'Oncology', 'photo' => null],
            ['id' => 4, 'name' => 'Hiroshi Tanaka', 'location' => 'Osaka, Japan', 'rating' => 5, 'quote' => 'Robotic surgery and a recovery plan that put me back at work in three weeks.', 'treatment' => 'Spine surgery', 'photo' => null],
        ]);
    }
}
