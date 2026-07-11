<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('topbar')->nullable();
            $table->json('header')->nullable();
            $table->json('footer')->nullable();
            $table->json('hero')->nullable();
            $table->json('home_sections')->nullable();
            $table->json('about')->nullable();
            $table->json('career_stats')->nullable();
            $table->json('contact_page')->nullable();
            $table->json('appointment_sidebar')->nullable();
            $table->json('careers_page')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'topbar', 'header', 'footer', 'hero', 'home_sections',
                'about', 'career_stats', 'contact_page', 'appointment_sidebar', 'careers_page',
            ]);
        });
    }
};
