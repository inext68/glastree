<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viste_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nome');
            $table->string('tipo'); // individui, gruppi, documenti, eventi
            $table->json('colonne_visibili')->nullable();
            $table->json('colonne_ordinamento')->nullable(); // [['colonna', 'direzione']]
            $table->json('filtri')->nullable(); // [['colonna', 'operatore', 'valore']]
            $table->string('ricerca')->nullable();
            $table->timestamps();
            $table->index('tipo');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viste_report');
    }
};