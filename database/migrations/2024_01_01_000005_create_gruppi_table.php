<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gruppi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('gruppi')->onDelete('set null');
            $table->foreignId('diocesi_id')->nullable()->constrained('diocesi')->onDelete('set null');
            $table->foreignId('responsabile_id')->nullable()->constrained('individui')->onDelete('set null');
            $table->string('nome');
            $table->text('descrizione')->nullable();
            $table->string('indirizzo_incontro')->nullable();
            $table->string('cap_incontro', 10)->nullable();
            $table->string('città_incontro')->nullable();
            $table->string('sigla_provincia_incontro', 2)->nullable();
            $table->string('mappa_posizione')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index('tenant_id');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gruppi');
    }
};