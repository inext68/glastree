<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('individui', function (Blueprint $table) {
            $table->string('numero_documento', 50)->nullable()->after('tipo_documento');
        });
    }

    public function down(): void
    {
        Schema::table('individui', function (Blueprint $table) {
            $table->dropColumn('numero_documento');
        });
    }
};