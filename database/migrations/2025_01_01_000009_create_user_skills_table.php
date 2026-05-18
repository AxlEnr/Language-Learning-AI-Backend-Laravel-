<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_skills', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('skill', ['vocabulary', 'grammar', 'listening', 'speaking']);
            $table->unsignedSmallInteger('level')->default(0);
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'skill']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skills');
    }
};
