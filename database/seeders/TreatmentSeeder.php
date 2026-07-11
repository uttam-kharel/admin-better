<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        Treatment::insert([
            ['id' => 1, 'slug' => 'robotic-knee', 'name' => 'Robotic Knee Replacement', 'summary' => 'Sub-millimeter precision for faster recovery', 'image' => 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=1200&q=80'],
            ['id' => 2, 'slug' => 'tavr', 'name' => 'TAVR Procedure', 'summary' => 'Minimally invasive aortic valve replacement', 'image' => 'https://images.unsplash.com/photo-1631815589968-fdb09a223b1e?auto=format&fit=crop&w=1200&q=80'],
            ['id' => 3, 'slug' => 'ivf', 'name' => 'IVF & Fertility', 'summary' => 'Advanced reproductive medicine programs', 'image' => 'https://images.unsplash.com/photo-1581056771107-24ca5f033842?auto=format&fit=crop&w=1200&q=80'],
            ['id' => 4, 'slug' => 'bariatric', 'name' => 'Bariatric Surgery', 'summary' => 'Weight management with multidisciplinary care', 'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80'],
        ]);
    }
}
