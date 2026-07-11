<?php

namespace Database\Seeders;

use App\Models\Award;
use Illuminate\Database\Seeder;

class AwardSeeder extends Seeder
{
    public function run(): void
    {
        Award::insert([
            ['id' => 1, 'title' => 'JCI Accredited Hospital', 'issuer' => 'Joint Commission International', 'year' => 2024, 'icon' => 'trophy'],
            ['id' => 2, 'title' => 'NABH Accreditation', 'issuer' => 'National Accreditation Board', 'year' => 2024, 'icon' => 'shield-check'],
            ['id' => 3, 'title' => 'Best Cardiac Center', 'issuer' => 'Healthcare Excellence Awards', 'year' => 2023, 'icon' => 'trophy'],
            ['id' => 4, 'title' => 'ISO 9001:2015 Certified', 'issuer' => 'ISO', 'year' => 2024, 'icon' => 'check-badge'],
        ]);
    }
}
