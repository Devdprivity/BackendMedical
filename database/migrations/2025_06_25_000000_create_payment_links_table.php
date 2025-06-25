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
        Schema::create('payment_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique(); // Token único para el link
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade'); // Doctor que genera el link
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade'); // Paciente específico (opcional)
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade'); // Método de pago
            
            // Información del pago
            $table->decimal('amount', 10, 2); // Monto a pagar
            $table->string('currency', 3); // Moneda
            $table->string('concept'); // Concepto del pago
            $table->text('description')->nullable(); // Descripción adicional
            
            // Control de tiempo y seguridad
            $table->timestamp('expires_at'); // Cuándo expira el link
            $table->timestamp('viewed_at')->nullable(); // Cuándo fue visto por primera vez
            $table->boolean('is_active')->default(true); // Si está activo
            $table->boolean('is_used')->default(false); // Si ya fue usado para pagar
            
            // Información del pago realizado
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'expired'])->default('pending');
            $table->foreignId('appointment_payment_id')->nullable()->constrained('appointment_payments')->onDelete('set null');
            $table->timestamp('paid_at')->nullable(); // Cuándo se completó el pago
            
            // Metadatos
            $table->json('metadata')->nullable(); // Información adicional (IP, user agent, etc.)
            $table->integer('view_count')->default(0); // Número de veces que se ha visto
            
            $table->timestamps();
            
            // Índices
            $table->index(['token']);
            $table->index(['doctor_id', 'created_at']);
            $table->index(['patient_id', 'payment_status']);
            $table->index(['expires_at', 'is_active']);
            $table->index(['payment_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_links');
    }
}; 