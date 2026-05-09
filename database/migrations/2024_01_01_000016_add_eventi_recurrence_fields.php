<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventi', function (Blueprint $table) {
            $table->string('giorno_mese')->nullable()->after('giorno_settimana');
            $table->string('mesi_recorrenza')->nullable()->after('giorno_mese');
            $table->string('mese_annuale')->nullable()->after('mesi_recorrenza');
        });
    }

    public function down(): void
    {
        Schema::table('eventi', function (Blueprint $table) {
            $table->dropColumn('giorno_mese');
            $table->dropColumn('mesi_recorrenza');
            $table->dropColumn('mese_annuale');
        });
    }
};