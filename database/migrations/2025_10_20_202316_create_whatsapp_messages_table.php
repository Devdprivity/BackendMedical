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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            // Información del mensaje
            $table->enum('message_type', ['reminder', 'confirmation_request', 'reschedule_offer', 'cancellation', 'custom'])->default('custom');
            $table->enum('direction', ['outbound', 'inbound'])->default('outbound');
            $table->text('content');
            $table->string('template_name', 100)->nullable();

            // Estado
            $table->enum('status', ['queued', 'sending', 'sent', 'delivered', 'read', 'failed', 'rate_limited'])->default('queued');
            $table->datetime('scheduled_for')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->datetime('delivered_at')->nullable();
            $table->datetime('read_at')->nullable();
            $table->datetime('failed_at')->nullable();
            $table->text('failure_reason')->nullable();

            // WhatsApp IDs
            $table->string('whatsapp_message_id', 255)->nullable();
            $table->string('patient_whatsapp_number', 20);

            // Tracking de respuestas
            $table->boolean('requires_response')->default(false);
            $table->boolean('response_received')->default(false);
            $table->string('response_type', 50)->nullable(); // 'confirm', 'reschedule', 'cancel', 'other'
            $table->text('response_content')->nullable();

            // Metadata
            $table->json('metadata')->nullable(); // Variables del template, botones, etc.

            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_for');
            $table->index('appointment_id');
            $table->index('patient_whatsapp_number');
            $table->index(['whatsapp_account_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
