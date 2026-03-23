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
        Schema::create('appointment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('whatsapp_message_id')->nullable()->constrained('whatsapp_messages')->onDelete('set null');

            // Estado de confirmación
            $table->enum('confirmation_status', ['pending', 'confirmed', 'reschedule_requested', 'cancelled', 'no_response'])->default('pending');
            $table->enum('confirmation_method', ['whatsapp', 'phone', 'email', 'in_person', 'system']);

            // Recordatorios
            $table->datetime('reminder_sent_at')->nullable();
            $table->integer('reminder_count')->default(0);
            $table->datetime('next_reminder_at')->nullable();

            // Respuesta del paciente
            $table->text('patient_response')->nullable();
            $table->datetime('responded_at')->nullable();

            // Reagendamiento
            $table->text('reschedule_reason')->nullable();
            $table->foreignId('new_appointment_id')->nullable()->constrained('appointments')->onDelete('set null');

            $table->timestamps();

            $table->index('appointment_id');
            $table->index('confirmation_status');
            $table->index('next_reminder_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_confirmations');
    }
};
