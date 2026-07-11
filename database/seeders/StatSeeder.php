<?php

namespace Database\Seeders;

use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatSeeder extends Seeder
{
    public function run(): void
    {
        Stat::insert([
            ['id' => 1, 'value' => '1,200+', 'label' => 'Beds'],
            ['id' => 2, 'value' => '450+', 'label' => 'Consultants'],
            ['id' => 3, 'value' => '1M+', 'label' => 'Patients Served'],
            ['id' => 4, 'value' => '25+', 'label' => 'Years of Trust'],
        ]);
    }
}
