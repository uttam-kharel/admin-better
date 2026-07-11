<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        Technology::insert([
            ['id' => 1, 'name' => 'Da Vinci Xi Surgical Robot', 'summary' => 'Minimally invasive precision surgery', 'icon' => 'cpu-chip'],
            ['id' => 2, 'name' => '3T MRI Scanner', 'summary' => 'Ultra-high-field neurological imaging', 'icon' => 'document-magnifying-glass'],
            ['id' => 3, 'name' => 'PET-CT Suite', 'summary' => 'Functional & metabolic oncology imaging', 'icon' => 'sparkles'],
            ['id' => 4, 'name' => 'Linear Accelerator', 'summary' => 'Image-guided radiation therapy', 'icon' => 'signal'],
        ]);
    }
}
