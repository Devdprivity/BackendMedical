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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->enum('message_type', ['reminder', 'confirmation_request', 'reschedule_offer', 'cancellation', 'custom']);

            // Contenido
            $table->text('content');
            $table->json('variables')->nullable(); // Lista de variables: {{patient_name}}, {{doctor_name}}, etc.

            // Configuración
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->string('language', 5)->default('es');

            // Para WhatsApp Business API (futuro)
            $table->string('whatsapp_template_id', 255)->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'not_submitted'])->default('not_submitted');

            $table->timestamps();

            $table->unique(['clinic_id', 'name']);
            $table->index('message_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
