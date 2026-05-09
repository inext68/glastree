<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mailing_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->string('nome');
            $table->text('descrizione')->nullable();
            $table->boolean('attiva')->default(true);
            $table->timestamps();
            $table->index('tenant_id');
        });

        Schema::create('mailing_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mailing_list_id')->constrained('mailing_lists')->onDelete('cascade');
            $table->foreignId('individuo_id')->constrained('individui')->onDelete('cascade');
            $table->boolean('opt_in')->default(true);
            $table->dateTime('opt_in_data')->nullable();
            $table->dateTime('opt_out_data')->nullable();
            $table->timestamps();
            $table->unique(['mailing_list_id', 'individuo_id']);
        });

        Schema::create('mailing_messaggi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->foreignId('mailing_list_id')->nullable()->constrained('mailing_lists')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('oggetto');
            $table->text('corpo')->nullable();
            $table->enum('canale', ['email', 'telegram'])->default('email');
            $table->enum('stato', ['bozza', 'in_coda', 'inviato', 'fallito'])->default('bozza');
            $table->timestamp('inviato_al')->nullable();
            $table->integer('totale_destinatari')->default(0);
            $table->integer('invii_ok')->default(0);
            $table->integer('invii_falliti')->default(0);
            $table->timestamps();
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mailing_messaggi');
        Schema::dropIfExists('mailing_contacts');
        Schema::dropIfExists('mailing_lists');
    }
};