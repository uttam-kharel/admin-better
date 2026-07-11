<?php

namespace Database\Seeders;

use App\Models\InsurancePartner;
use Illuminate\Database\Seeder;

class InsurancePartnerSeeder extends Seeder
{
    public function run(): void
    {
        InsurancePartner::insert([
            ['id' => 1, 'name' => 'Aetna', 'logo' => ''],
            ['id' => 2, 'name' => 'Cigna', 'logo' => ''],
            ['id' => 3, 'name' => 'BUPA', 'logo' => ''],
            ['id' => 4, 'name' => 'Allianz', 'logo' => ''],
            ['id' => 5, 'name' => 'MaxBupa', 'logo' => ''],
            ['id' => 6, 'name' => 'Star Health', 'logo' => ''],
        ]);
    }
}
