<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quick_actions', function (Blueprint $table) {
            $table->id();
            $table->string('icon');
            $table->string('label');
            $table->string('helper');
            $table->string('url');
            $table->string('tone');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_actions');
    }
};
