<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::insert([
            'site_name' => 'Shubham International Hospital',
            'tagline' => 'Advanced medical care for every generation',
            'logo_text' => 'L',
            'emergency_phone' => '1800-123-4567',
            'primary_phone' => '+1-800-123-4567',
            'email' => 'contact@lumina.health',
            'address' => '1 Health Plaza, Cityname, Country',
            'facebook' => 'https://facebook.com/luminahealth',
            'twitter' => 'https://twitter.com/luminahealth',
            'instagram' => 'https://instagram.com/luminahealth',
            'linkedin' => 'https://linkedin.com/company/luminahealth',
            'youtube' => 'https://youtube.com/@luminahealth',
        ]);
    }
}
