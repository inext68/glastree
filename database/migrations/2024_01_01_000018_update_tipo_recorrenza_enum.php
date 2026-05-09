<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventi', function (Blueprint $table) {
            $table->enum('tipo_recorrenza', ['singolo', 'settimanale', 'mensile', 'annuale', 'altro'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('eventi', function (Blueprint $table) {
            $table->enum('tipo_recorrenza', ['singolo', 'ricorrente'])->default('singolo')->change();
        });
    }
};