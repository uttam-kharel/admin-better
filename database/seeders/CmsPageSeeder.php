<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['slug' => 'about-us', 'title' => 'About Shubham International Hospital', 'meta_title' => 'About — Shubham International Hospital', 'meta_description' => 'A 25-year legacy of patient-first medical care.', 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'A 25-year legacy of patient-first care', 'subtitle' => 'Defining the standard of clinical excellence.']],
                ['type' => 'richText', 'data' => ['html' => '<p>Shubham International Hospital operates a connected system of multi-specialty hospitals across three continents, delivering advanced medical care grounded in research, technology, and human compassion.</p><p>Our mission is to combine specialized expertise with compassionate care to deliver world-class medical outcomes for every patient we serve.</p>']],
                ['type' => 'cta', 'data' => ['title' => 'Plan your visit', 'subtitle' => 'Speak with our patient liaison team today.', 'ctaLabel' => 'Contact us', 'ctaUrl' => '/contact']],
            ]],
            ['slug' => 'quality', 'title' => 'Quality & Accreditation', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Quality you can verify']],
                ['type' => 'richText', 'data' => ['html' => '<p>We hold JCI, NABH, and ISO 9001:2015 accreditations and report outcome metrics publicly each quarter.</p>']],
            ]],
            ['slug' => 'online-reports', 'title' => 'Online Report Verification', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Verify your medical reports online']],
                ['type' => 'richText', 'data' => ['html' => '<p>Enter your Patient ID and report number on the patient portal to download verified lab and imaging reports.</p>']],
                ['type' => 'cta', 'data' => ['title' => 'Open patient portal', 'ctaLabel' => 'Login', 'ctaUrl' => '/pages/patient-portal']],
            ]],
            ['slug' => 'patient-portal', 'title' => 'Patient Portal', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Your health, in one secure place']],
                ['type' => 'richText', 'data' => ['html' => '<p>The patient portal gives you 24/7 access to appointments, reports, billing, and secure messaging with your care team.</p>']],
            ]],
            ['slug' => 'insurance', 'title' => 'Insurance & TPA Partners', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Insurance & TPA partners']],
                ['type' => 'richText', 'data' => ['html' => '<p>We are empaneled with all leading insurers and third-party administrators. Cashless authorization is typically completed within 4 hours.</p>']],
            ]],
            ['slug' => 'international-patients', 'title' => 'International Patients', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Travel for care, with confidence']],
                ['type' => 'richText', 'data' => ['html' => '<p>Our International Patient Services desk coordinates visas, travel, accommodation, language support, and treatment planning end-to-end.</p>']],
                ['type' => 'cta', 'data' => ['title' => 'Plan your treatment', 'ctaLabel' => 'Request a quote', 'ctaUrl' => '/contact']],
            ]],
            ['slug' => 'medical-tourism', 'title' => 'Medical Tourism', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Medical tourism, simplified']],
                ['type' => 'richText', 'data' => ['html' => '<p>From your first inquiry through to follow-up after returning home, our team handles every detail of your treatment journey.</p>']],
            ]],
            ['slug' => 'ambulance', 'title' => 'Ambulance Services', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => '24/7 advanced life support']],
                ['type' => 'richText', 'data' => ['html' => '<p>Our fleet of advanced life support ambulances is staffed by paramedics and equipped with cardiac monitors, ventilators, and tele-link to specialists.</p>']],
            ]],
            ['slug' => 'downloads', 'title' => 'Download Center', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Forms & resources']],
                ['type' => 'richText', 'data' => ['html' => '<p>Download patient registration forms, insurance authorization templates, and pre-procedure instructions.</p>']],
            ]],
            ['slug' => 'executive-checkup', 'title' => 'Executive Health Checkups', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Executive health, in one day']],
                ['type' => 'richText', 'data' => ['html' => '<p>A single-day comprehensive screening designed for busy professionals, with results and lifestyle recommendations delivered the same evening.</p>']],
            ]],
            ['slug' => 'events', 'title' => 'Events & Camps', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Health camps & community events']],
                ['type' => 'richText', 'data' => ['html' => '<p>Free screening camps, awareness workshops, and CME events for healthcare professionals are organized throughout the year.</p>']],
            ]],
            ['slug' => 'awards', 'title' => 'Awards & Recognition', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Recognized for clinical excellence']],
                ['type' => 'richText', 'data' => ['html' => '<p>From JCI accreditation to national clinical excellence awards, our recognitions reflect a continuous commitment to quality.</p>']],
            ]],
            ['slug' => 'why-choose-us', 'title' => 'Why Choose Us', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'features', 'data' => ['items' => [
                    ['title' => 'Patient-first care model', 'body' => 'Care plans designed around your goals, schedule, and family.'],
                    ['title' => 'Multidisciplinary teams', 'body' => 'Cross-specialty collaboration on every complex case.'],
                    ['title' => 'Outcomes you can verify', 'body' => 'Quarterly public reporting of clinical outcome metrics.'],
                    ['title' => 'End-to-end coordination', 'body' => 'From first call to follow-up, one team owns your journey.'],
                ]]],
            ]],
            ['slug' => 'csr', 'title' => 'CSR Activities', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Healthcare for every community']],
                ['type' => 'richText', 'data' => ['html' => '<p>Our CSR programs include rural health camps, school screening drives, and partnerships with public health initiatives.</p>']],
            ]],
            ['slug' => 'careers', 'title' => 'Careers', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Build your career in healthcare']],
                ['type' => 'richText', 'data' => ['html' => '<p>Join a team of 1,200+ clinicians and healthcare professionals dedicated to advancing patient care.</p>']],
            ]],
            ['slug' => 'blood-bank', 'title' => 'Blood Bank', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Donate. Save lives.']],
                ['type' => 'richText', 'data' => ['html' => '<p>Our NABH-accredited blood bank operates 24/7 and welcomes voluntary donors. One donation can save up to three lives.</p>']],
            ]],
            ['slug' => 'organ-donation', 'title' => 'Organ Donation', 'meta_title' => null, 'meta_description' => null, 'og_image' => null, 'blocks' => [
                ['type' => 'hero', 'data' => ['title' => 'Pledge to give a life']],
                ['type' => 'richText', 'data' => ['html' => '<p>Learn about deceased and living organ donation programs and pledge your organs in support of our transplant program.</p>']],
            ]],
        ];

        foreach ($pages as $page) {
            CmsPage::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
