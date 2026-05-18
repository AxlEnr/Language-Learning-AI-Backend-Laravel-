<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('words', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('word');
            $table->text('meaning')->nullable();
            $table->text('example_sentence')->nullable();
            $table->timestamps();

            $table->unique(['language_id', 'word']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
