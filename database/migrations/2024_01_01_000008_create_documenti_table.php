<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->string('nome_file');
            $table->string('file_path');
            $table->string('tipo')->nullable();
            $table->enum('tipologia', ['avatar', 'galleria', 'documento', 'statuto', 'altro'])->default('documento');
            $table->enum('visibilita', ['pubblico', 'gruppo', 'individuo', 'associazione', 'federazione'])->default('gruppo');
            $table->unsignedBigInteger('visibilita_target_id')->nullable();
            $table->string('visibilita_target_type')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('dimensione')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documenti');
    }
};