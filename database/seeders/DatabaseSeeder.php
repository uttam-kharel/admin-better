<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteSettingSeeder::class,
            MenuItemSeeder::class,
            HeroSlideSeeder::class,
            QuickActionSeeder::class,
            StatSeeder::class,
            DepartmentSeeder::class,
            DoctorSeeder::class,
            ServiceSeeder::class,
            HealthPackageSeeder::class,
            TreatmentSeeder::class,
            TechnologySeeder::class,
            TestimonialSeeder::class,
            PatientStorySeeder::class,
            InsurancePartnerSeeder::class,
            JobOpeningSeeder::class,
            AwardSeeder::class,
            BlogPostSeeder::class,
            AuthorSeeder::class,
            FaqSeeder::class,
            GalleryItemSeeder::class,
            AppointmentSeeder::class,
            ContactSubmissionSeeder::class,
            CmsPageSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
