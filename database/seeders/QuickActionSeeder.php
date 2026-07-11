<?php

namespace Database\Seeders;

use App\Models\QuickAction;
use Illuminate\Database\Seeder;

class QuickActionSeeder extends Seeder
{
    public function run(): void
    {
        QuickAction::insert([
            ['id' => 1, 'icon' => 'truck', 'label' => 'Call Ambulance', 'helper' => '24/7 rapid response', 'url' => 'tel:18001234567', 'tone' => 'emergency'],
            ['id' => 2, 'icon' => 'calendar-days', 'label' => 'Book Appointment', 'helper' => 'Real-time scheduling', 'url' => '/appointment', 'tone' => 'primary'],
            ['id' => 3, 'icon' => 'document-text', 'label' => 'Online Reports', 'helper' => 'Secure patient portal', 'url' => '/pages/online-reports', 'tone' => 'secondary'],
            ['id' => 4, 'icon' => 'magnifying-glass', 'label' => 'Find a Doctor', 'helper' => 'Search by specialty', 'url' => '/doctors', 'tone' => 'neutral'],
        ]);
    }
}
