<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::insert([
            ['id' => 1, 'slug' => 'emergency-services', 'name' => 'Emergency Services', 'summary' => '24/7 Level-1 trauma response', 'description' => 'Round-the-clock emergency care with rapid triage, advanced trauma teams, and ambulance services.', 'icon' => 'bell-alert', 'department_slug' => null],
            ['id' => 2, 'slug' => 'diagnostic-imaging', 'name' => 'Diagnostic Imaging', 'summary' => 'MRI, CT, PET-CT, Ultrasound', 'description' => 'Full-spectrum imaging with the latest 3T MRI and 256-slice CT scanners.', 'icon' => 'document-magnifying-glass', 'department_slug' => null],
            ['id' => 3, 'slug' => 'laboratory', 'name' => 'Laboratory Services', 'summary' => 'NABL-accredited diagnostic lab', 'description' => 'Routine and specialized tests with rapid digital reporting.', 'icon' => 'beaker', 'department_slug' => null],
            ['id' => 4, 'slug' => 'pharmacy', 'name' => 'Pharmacy', 'summary' => '24-hour in-house pharmacy', 'description' => 'Comprehensive pharmacy with home delivery for chronic medications.', 'icon' => 'clipboard-document-check', 'department_slug' => null],
            ['id' => 5, 'slug' => 'physiotherapy', 'name' => 'Physiotherapy & Rehab', 'summary' => 'Recovery and sports medicine', 'description' => 'Modern physiotherapy gym with sports rehabilitation specialists.', 'icon' => 'heart', 'department_slug' => null],
            ['id' => 6, 'slug' => 'telemedicine', 'name' => 'Telemedicine', 'summary' => 'Virtual consultations', 'description' => 'Connect with our specialists from anywhere via secure video consultations.', 'icon' => 'video-camera', 'department_slug' => null],
        ]);
    }
}
