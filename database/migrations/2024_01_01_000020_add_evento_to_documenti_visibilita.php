<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documenti', function (Blueprint $table) {
            $table->enum('visibilita', ['pubblico', 'gruppo', 'individuo', 'associazione', 'federazione', 'evento'])->default('gruppo')->change();
        });
    }

    public function down(): void
    {
        Schema::table('documenti', function (Blueprint $table) {
            $table->enum('visibilita', ['pubblico', 'gruppo', 'individuo', 'associazione', 'federazione'])->default('gruppo')->change();
        });
    }
};