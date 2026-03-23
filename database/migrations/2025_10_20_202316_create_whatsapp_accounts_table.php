<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone_number', 20)->unique();
            $table->string('display_name', 100)->nullable();
            $table->enum('status', ['active', 'inactive', 'warming_up', 'banned', 'cooldown'])->default('warming_up');
            $table->enum('type', ['venom_bot', 'official_api'])->default('venom_bot');
            $table->date('registration_date');
            $table->integer('days_since_registration')->default(0);

            // Límites y contadores
            $table->integer('daily_message_limit')->default(20);
            $table->integer('hourly_message_limit')->default(15);
            $table->integer('messages_sent_today')->default(0);
            $table->integer('messages_sent_this_hour')->default(0);
            $table->integer('new_contacts_today')->default(0);

            // Métricas de salud
            $table->decimal('response_rate', 5, 2)->default(0.00);
            $table->integer('total_messages_sent')->default(0);
            $table->integer('total_responses_received')->default(0);
            $table->datetime('last_ban_date')->nullable();
            $table->integer('ban_count')->default(0);

            // Configuración
            $table->text('session_data')->nullable(); // JSON con datos de sesión de Venom
            $table->json('api_credentials')->nullable(); // Para WhatsApp Business API

            $table->timestamps();

            $table->index('status');
            $table->index('clinic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_accounts');
    }
};
