<?php

namespace Database\Seeders;

use App\Models\HealthPackage;
use Illuminate\Database\Seeder;

class HealthPackageSeeder extends Seeder
{
    public function run(): void
    {
        HealthPackage::insert([
            ['id' => 1, 'slug' => 'wellness-check', 'name' => 'Wellness Check', 'tier' => 'essential', 'price' => 199, 'original_price' => 299, 'currency' => 'USD', 'description' => 'Routine screening for basic health indicators.', 'inclusions' => json_encode(['Complete blood profile', 'BMI analysis', 'Doctor consultation', 'Urine analysis']), 'is_popular' => false],
            ['id' => 2, 'slug' => 'executive-health', 'name' => 'Executive Health', 'tier' => 'comprehensive', 'price' => 499, 'original_price' => 699, 'currency' => 'USD', 'description' => 'In-depth metabolic and cardiac assessment.', 'inclusions' => json_encode(['Cardiac stress test', 'Full imaging panel', 'Nutrition expert', 'Ophthalmology', 'Specialist consults']), 'is_popular' => true],
            ['id' => 3, 'slug' => 'heart-vascular', 'name' => 'Heart & Vascular', 'tier' => 'specialized', 'price' => 349, 'original_price' => null, 'currency' => 'USD', 'description' => 'Targeted screening for cardiovascular risks.', 'inclusions' => json_encode(['Advanced ECG', 'Lipid profile', 'Echocardiogram', 'Cardiologist consult']), 'is_popular' => false],
            ['id' => 4, 'slug' => 'women-wellness', 'name' => "Women's Wellness", 'tier' => 'comprehensive', 'price' => 379, 'original_price' => 459, 'currency' => 'USD', 'description' => 'Comprehensive screening tailored for women.', 'inclusions' => json_encode(['Pap smear', 'Mammography', 'Bone density', 'Gynecology consult']), 'is_popular' => false],
        ]);
    }
}
