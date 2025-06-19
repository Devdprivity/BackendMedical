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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Doctor/Admin que configura
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->onDelete('cascade');
            $table->enum('type', ['paypal', 'binance_pay', 'pago_movil', 'stripe', 'wepay']); // Tipos de pago
            $table->boolean('is_active')->default(true);
            
            // Datos específicos por método de pago (JSON)
            $table->json('config'); // Configuración específica del método
            
            // PayPal: paypal_email
            // Binance Pay: binance_id, default_currency, supported_networks
            // Pago Móvil: receiver_phone, receiver_cedula, receiver_bank, receiver_name
            // Stripe: stripe_account_id, webhook_secret
            // WePay: wepay_account_id
            
            $table->decimal('consultation_fee', 10, 2)->default(0); // Tarifa por consulta
            $table->string('currency', 3)->default('USD'); // Moneda
            $table->text('instructions')->nullable(); // Instrucciones adicionales para el paciente
            $table->integer('order')->default(0); // Orden de preferencia
            
            $table->timestamps();
            
            // Índices
            $table->index(['user_id', 'type', 'is_active']);
            $table->index(['clinic_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
