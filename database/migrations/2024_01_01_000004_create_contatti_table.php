<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contatti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('individuo_id')->constrained('individui')->onDelete('cascade');
            $table->enum('tipo', ['telefono', 'cellulare', 'email', 'fax', 'web', 'telegram', 'whatsapp', 'altro']);
            $table->string('valore');
            $table->string('etichetta')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->index('individuo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contatti');
    }
};