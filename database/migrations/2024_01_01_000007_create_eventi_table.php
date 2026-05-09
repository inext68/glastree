<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->string('nome_evento');
            $table->string('tipo_evento')->nullable();
            $table->enum('tipo_recorrenza', ['singolo', 'ricorrente'])->default('singolo');
            $table->unsignedTinyInteger('giorno_settimana')->nullable();
            $table->time('ora_inizio')->nullable();
            $table->date('data_specifica')->nullable();
            $table->unsignedInteger('durata_minuti')->nullable();
            $table->text('descrizione')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('tenant_id');
        });

        Schema::create('eventi_gruppi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventi')->onDelete('cascade');
            $table->foreignId('gruppo_id')->constrained('gruppi')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('eventi_responsabili', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventi')->onDelete('cascade');
            $table->foreignId('individuo_id')->constrained('individui')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventi_responsabili');
        Schema::dropIfExists('eventi_gruppi');
        Schema::dropIfExists('eventi');
    }
};