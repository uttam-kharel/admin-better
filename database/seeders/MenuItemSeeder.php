<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        MenuItem::insert([
            ['id' => 1, 'parent_id' => null, 'title' => 'Specialties', 'slug' => 'specialties', 'type' => 'mega', 'url' => null, 'icon' => null, 'description' => null, 'order' => 0],
            ['id' => 2, 'parent_id' => null, 'title' => 'Find a Doctor', 'slug' => 'doctors', 'type' => 'link', 'url' => '/doctors', 'icon' => null, 'description' => null, 'order' => 1],
            ['id' => 3, 'parent_id' => null, 'title' => 'Patients & Visitors', 'slug' => 'patients', 'type' => 'dropdown', 'url' => null, 'icon' => null, 'description' => null, 'order' => 2],
            ['id' => 4, 'parent_id' => null, 'title' => 'Health & Wellness', 'slug' => 'wellness', 'type' => 'dropdown', 'url' => null, 'icon' => null, 'description' => null, 'order' => 3],
            ['id' => 5, 'parent_id' => null, 'title' => 'About', 'slug' => 'about', 'type' => 'dropdown', 'url' => null, 'icon' => null, 'description' => null, 'order' => 4],
            ['id' => 6, 'parent_id' => null, 'title' => 'Careers', 'slug' => 'careers', 'type' => 'link', 'url' => '/careers', 'icon' => null, 'description' => 'Join our team', 'order' => 5],
        ]);

        MenuItem::insert([
            ['id' => 11, 'parent_id' => 1, 'title' => 'Cardiology', 'slug' => 'cardiology', 'type' => 'link', 'url' => '/departments/cardiology', 'icon' => null, 'description' => 'Heart & vascular care', 'order' => 0],
            ['id' => 12, 'parent_id' => 1, 'title' => 'Neurology', 'slug' => 'neurology', 'type' => 'link', 'url' => '/departments/neurology', 'icon' => null, 'description' => 'Brain & nervous system', 'order' => 1],
            ['id' => 13, 'parent_id' => 1, 'title' => 'Oncology', 'slug' => 'oncology', 'type' => 'link', 'url' => '/departments/oncology', 'icon' => null, 'description' => 'Cancer treatment', 'order' => 2],
            ['id' => 14, 'parent_id' => 1, 'title' => 'Orthopedics', 'slug' => 'orthopedics', 'type' => 'link', 'url' => '/departments/orthopedics', 'icon' => null, 'description' => 'Bones & joints', 'order' => 3],
            ['id' => 15, 'parent_id' => 1, 'title' => 'Pediatrics', 'slug' => 'pediatrics', 'type' => 'link', 'url' => '/departments/pediatrics', 'icon' => null, 'description' => 'Child healthcare', 'order' => 4],
            ['id' => 16, 'parent_id' => 1, 'title' => 'Gastroenterology', 'slug' => 'gastroenterology', 'type' => 'link', 'url' => '/departments/gastroenterology', 'icon' => null, 'description' => 'Digestive system', 'order' => 5],
            ['id' => 17, 'parent_id' => 1, 'title' => 'Ophthalmology', 'slug' => 'ophthalmology', 'type' => 'link', 'url' => '/departments/ophthalmology', 'icon' => null, 'description' => 'Eye care', 'order' => 6],
            ['id' => 18, 'parent_id' => 1, 'title' => 'Internal Medicine', 'slug' => 'internal-medicine', 'type' => 'link', 'url' => '/departments/internal-medicine', 'icon' => null, 'description' => 'Adult primary care', 'order' => 7],
            ['id' => 31, 'parent_id' => 3, 'title' => 'Book Appointment', 'slug' => 'book', 'type' => 'link', 'url' => '/appointment', 'icon' => null, 'description' => null, 'order' => 0],
            ['id' => 32, 'parent_id' => 3, 'title' => 'Online Report Verification', 'slug' => 'reports', 'type' => 'link', 'url' => '/pages/online-reports', 'icon' => null, 'description' => null, 'order' => 1],
            ['id' => 33, 'parent_id' => 3, 'title' => 'Patient Portal Login', 'slug' => 'portal', 'type' => 'link', 'url' => '/pages/patient-portal', 'icon' => null, 'description' => null, 'order' => 2],
            ['id' => 34, 'parent_id' => 3, 'title' => 'Insurance & TPA Partners', 'slug' => 'insurance', 'type' => 'link', 'url' => '/pages/insurance', 'icon' => null, 'description' => null, 'order' => 3],
            ['id' => 35, 'parent_id' => 3, 'title' => 'International Patients', 'slug' => 'international', 'type' => 'link', 'url' => '/pages/international-patients', 'icon' => null, 'description' => null, 'order' => 4],
            ['id' => 36, 'parent_id' => 3, 'title' => 'Medical Tourism', 'slug' => 'tourism', 'type' => 'link', 'url' => '/pages/medical-tourism', 'icon' => null, 'description' => null, 'order' => 5],
            ['id' => 37, 'parent_id' => 3, 'title' => 'Ambulance Services', 'slug' => 'ambulance', 'type' => 'link', 'url' => '/pages/ambulance', 'icon' => null, 'description' => null, 'order' => 6],
            ['id' => 38, 'parent_id' => 3, 'title' => 'Download Center', 'slug' => 'downloads', 'type' => 'link', 'url' => '/pages/downloads', 'icon' => null, 'description' => null, 'order' => 7],
            ['id' => 41, 'parent_id' => 4, 'title' => 'Health Packages', 'slug' => 'packages', 'type' => 'link', 'url' => '/health-packages', 'icon' => null, 'description' => null, 'order' => 0],
            ['id' => 42, 'parent_id' => 4, 'title' => 'Executive Checkups', 'slug' => 'executive', 'type' => 'link', 'url' => '/pages/executive-checkup', 'icon' => null, 'description' => null, 'order' => 1],
            ['id' => 43, 'parent_id' => 4, 'title' => 'Health Library', 'slug' => 'library', 'type' => 'link', 'url' => '/blogs', 'icon' => null, 'description' => null, 'order' => 2],
            ['id' => 44, 'parent_id' => 4, 'title' => 'Events & Camps', 'slug' => 'events', 'type' => 'link', 'url' => '/pages/events', 'icon' => null, 'description' => null, 'order' => 3],
            ['id' => 51, 'parent_id' => 5, 'title' => 'About Us', 'slug' => 'about-us', 'type' => 'link', 'url' => '/pages/about-us', 'icon' => null, 'description' => null, 'order' => 0],
            ['id' => 52, 'parent_id' => 5, 'title' => 'Quality & Accreditation', 'slug' => 'quality', 'type' => 'link', 'url' => '/pages/quality', 'icon' => null, 'description' => null, 'order' => 1],
            ['id' => 53, 'parent_id' => 5, 'title' => 'Awards & Recognition', 'slug' => 'awards', 'type' => 'link', 'url' => '/pages/awards', 'icon' => null, 'description' => null, 'order' => 2],
            ['id' => 54, 'parent_id' => 5, 'title' => 'CSR Activities', 'slug' => 'csr', 'type' => 'link', 'url' => '/pages/csr', 'icon' => null, 'description' => null, 'order' => 3],
            ['id' => 56, 'parent_id' => 5, 'title' => 'Blood Bank', 'slug' => 'blood-bank', 'type' => 'link', 'url' => '/pages/blood-bank', 'icon' => null, 'description' => null, 'order' => 5],
            ['id' => 57, 'parent_id' => 5, 'title' => 'Organ Donation', 'slug' => 'organ-donation', 'type' => 'link', 'url' => '/pages/organ-donation', 'icon' => null, 'description' => null, 'order' => 6],
            ['id' => 58, 'parent_id' => 5, 'title' => 'Contact', 'slug' => 'contact', 'type' => 'link', 'url' => '/contact', 'icon' => null, 'description' => null, 'order' => 7],
        ]);
    }
}
