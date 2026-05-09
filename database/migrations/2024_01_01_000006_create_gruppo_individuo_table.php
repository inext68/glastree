<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gruppo_individuo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gruppo_id')->constrained('gruppi')->onDelete('cascade');
            $table->foreignId('individuo_id')->constrained('individui')->onDelete('cascade');
            $table->string('ruolo_nel_gruppo')->nullable();
            $table->date('data_adesione')->nullable();
            $table->timestamps();
            $table->unique(['gruppo_id', 'individuo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gruppo_individuo');
    }
};