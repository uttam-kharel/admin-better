<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            // Raw client IP (Vercel X-Forwarded-For). Also keep ip_hash for privacy-safe lookups.
            $table->string('ip', 45)->nullable()->index();
            // Parsed from the User-Agent.
            $table->string('os', 64)->nullable();
            // Primary Accept-Language tag.
            $table->string('language', 32)->nullable();
            // Geo lookup result cached from ip-api.com: {country, countryCode, city, regionName, isp, ...}
            $table->json('ip_info')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropColumn(['ip', 'os', 'language', 'ip_info']);
        });
    }
};
