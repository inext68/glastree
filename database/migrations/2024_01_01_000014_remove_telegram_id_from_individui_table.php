<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('individui', function (Blueprint $table) {
            $table->dropColumn('telegram_id');
        });
    }

    public function down(): void
    {
        Schema::table('individui', function (Blueprint $table) {
            $table->string('telegram_id', 100)->nullable()->after('note');
        });
    }
};