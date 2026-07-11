<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        Appointment::insert([
            ['id' => 'APT-100001', 'name' => 'Anita Sharma', 'email' => 'anita@example.com', 'phone' => '+919876543210', 'department_slug' => 'cardiology', 'doctor_slug' => 'sarah-chen', 'preferred_date' => date('Y-m-d', strtotime('+1 day')), 'message' => 'Follow-up consultation after angioplasty.', 'status' => 'pending', 'created_at' => now()->subHours(1), 'updated_at' => now()->subHours(1)],
            ['id' => 'APT-100002', 'name' => 'Robert Klein', 'email' => 'robert@example.com', 'phone' => '+4915123456789', 'department_slug' => 'orthopedics', 'doctor_slug' => 'rohan-mehta', 'preferred_date' => date('Y-m-d', strtotime('+3 days')), 'message' => 'Knee pain assessment.', 'status' => 'confirmed', 'created_at' => now()->subDay(), 'updated_at' => now()->subDay()],
        ]);
    }
}
