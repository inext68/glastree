<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('individui', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->string('codice_id', 5)->unique();
            $table->string('cognome');
            $table->string('nome');
            $table->date('data_nascita')->nullable();
            $table->string('indirizzo')->nullable();
            $table->string('cap', 10)->nullable();
            $table->string('città')->nullable();
            $table->string('sigla_provincia', 2)->nullable();
            $table->enum('genere', ['M', 'F'])->nullable();
            $table->enum('tipo_documento', ['carta_identita', 'patente'])->nullable();
            $table->date('scadenza_documento')->nullable();
            $table->text('note')->nullable();
            $table->string('avatar_path')->nullable();
            $table->timestamps();
            $table->index('tenant_id');
            $table->index('codice_id');
            $table->index('cognome');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('individui');
    }
};