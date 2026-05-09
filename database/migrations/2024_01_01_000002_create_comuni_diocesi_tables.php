<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diocesi', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('regione')->nullable();
            $table->timestamps();
        });

        Schema::create('comuni', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('codice_istat', 10)->nullable();
            $table->string('cap')->nullable();
            $table->string('sigla_provincia', 2)->nullable();
            $table->string('regione')->nullable();
            $table->float('latitudine')->nullable();
            $table->float('longitudine')->nullable();
            $table->timestamps();
            $table->index('nome');
            $table->index('sigla_provincia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comuni');
        Schema::dropIfExists('diocesi');
    }
};