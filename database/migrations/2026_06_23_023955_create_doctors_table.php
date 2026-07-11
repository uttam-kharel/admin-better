<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('designation');
            $table->string('department_slug');
            $table->string('department');
            $table->json('qualifications');
            $table->integer('experience_years');
            $table->json('languages');
            $table->string('photo')->nullable();
            $table->text('bio');
            $table->json('expertise');
            $table->json('schedule');
            $table->json('publications')->nullable();
            $table->timestamps();

            $table->foreign('department_slug')->references('slug')->on('departments');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
