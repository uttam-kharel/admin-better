<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Media columns were created as string() (varchar 255). Uploads are stored as
 * base64 data URIs when BLOB_READ_WRITE_TOKEN is not set (Vercel Blob not
 * provisioned), and full URLs can exceed 255 chars — both made saving a doctor
 * photo or CV throw SQLSTATE[22001] "value too long" on Postgres.
 *
 * Widen every column that can hold an image, file or URL to text.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'doctors' => ['photo'],
            'authors' => ['photo'],
            'departments' => ['icon', 'image'],
            'services' => ['icon'],
            'treatments' => ['image'],
            'blog_posts' => ['image'],
            'patient_stories' => ['image', 'url'],
            'testimonials' => ['photo'],
            'insurance_partners' => ['logo'],
            'hero_slides' => ['image', 'cta_url', 'secondary_cta_url'],
            'gallery_items' => ['url', 'thumbnail'],
            'quick_actions' => ['icon', 'url'],
            'menu_items' => ['icon', 'url'],
            'technologies' => ['icon'],
            'awards' => ['icon'],
            'job_applications' => ['resume_url'],
            'job_openings' => ['application_url'],
            'cms_pages' => ['og_image'],
            'site_settings' => ['logo_text'],
            'page_visits' => ['full_url'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column) {
                    if (! Schema::hasColumn($blueprint->getTable(), $column)) {
                        continue;
                    }
                    $blueprint->text($column)->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        // Reverting widths is unnecessary for correctness; leave as-is.
    }
};
