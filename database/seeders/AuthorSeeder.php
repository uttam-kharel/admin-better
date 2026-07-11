<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        Author::insert([
            ['id' => 1, 'slug' => 'sarah-chen', 'name' => 'Dr. Sarah Chen', 'photo' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80', 'bio' => 'Senior cardiologist with 18 years of experience in interventional cardiology and women\'s heart health.', 'specialty' => 'Cardiology'],
            ['id' => 2, 'slug' => 'james-wilson', 'name' => 'Dr. James Wilson', 'photo' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=400&q=80', 'bio' => 'Endocrinologist specializing in diabetes management and metabolic disorders.', 'specialty' => 'Endocrinology'],
            ['id' => 3, 'slug' => 'marcus-thorne', 'name' => 'Dr. Marcus Thorne', 'photo' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=400&q=80', 'bio' => 'Robotic surgeon and director of the minimally invasive surgery program.', 'specialty' => 'Robotic Surgery'],
            ['id' => 4, 'slug' => 'amara-okafor', 'name' => 'Dr. Amara Okafor', 'photo' => 'https://images.unsplash.com/photo-1594824476967-48c8b964273f?auto=format&fit=crop&w=400&q=80', 'bio' => 'Pediatrician with a focus on vaccination programs and adolescent health.', 'specialty' => 'Pediatrics'],
        ]);
    }
}
