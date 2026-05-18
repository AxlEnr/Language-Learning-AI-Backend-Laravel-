<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['vocabulary', 'grammar', 'listening', 'speaking']);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();

            $table->index('module_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
