<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventi_documenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventi')->onDelete('cascade');
            $table->foreignId('documento_id')->constrained('documenti')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventi_documenti');
    }
};