<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('query')->nullable();
            $table->string('full_url')->nullable();
            $table->string('referer')->nullable();
            $table->string('visitor_id', 64)->index();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device')->nullable();   // mobile | tablet | desktop
            $table->string('browser')->nullable(); // chrome | safari | firefox | ...
            $table->boolean('is_unique')->default(false)->index();
            $table->timestamps();

            $table->index('created_at');
            $table->index('path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
