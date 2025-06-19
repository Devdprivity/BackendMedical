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
        Schema::create('appointment_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            
            $table->string('payment_reference')->unique(); // Referencia única del pago
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_type', ['paypal', 'binance_pay', 'pago_movil', 'stripe', 'wepay']);
            
            $table->decimal('amount', 10, 2); // Monto pagado
            $table->string('currency', 3); // Moneda del pago
            $table->decimal('exchange_rate', 10, 4)->nullable(); // Tasa de cambio si aplica
            
            // Datos específicos del pago (JSON)
            $table->json('payment_data'); // Información específica según el método de pago
            
            // PayPal: transaction_id, payer_email, payment_status
            // Binance Pay: order_id, transaction_hash, network
            // Pago Móvil: reference_number, sender_phone, sender_bank, transaction_date
            // Stripe: charge_id, payment_intent_id
            // WePay: checkout_id, transaction_id
            
            $table->timestamp('paid_at')->nullable(); // Fecha del pago
            $table->timestamp('verified_at')->nullable(); // Fecha de verificación
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null'); // Quién verificó
            
            $table->text('notes')->nullable(); // Notas adicionales
            $table->string('receipt_url')->nullable(); // URL del comprobante
            
            $table->timestamps();
            
            // Índices
            $table->index(['appointment_id', 'status']);
            $table->index(['patient_id', 'status']);
            $table->index(['doctor_id', 'paid_at']);
            $table->index(['payment_reference']);
            $table->index(['status', 'payment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_payments');
    }
};
