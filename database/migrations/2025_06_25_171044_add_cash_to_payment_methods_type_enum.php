<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para PostgreSQL necesitamos modificar la restricción CHECK
        if (DB::getDriverName() === 'pgsql') {
            // Primero eliminar la restricción existente
            DB::statement("ALTER TABLE payment_methods DROP CONSTRAINT IF EXISTS payment_methods_type_check");
            
            // Crear la nueva restricción con el valor 'cash' incluido
            DB::statement("ALTER TABLE payment_methods ADD CONSTRAINT payment_methods_type_check CHECK (type IN ('paypal', 'binance_pay', 'pago_movil', 'stripe', 'wepay', 'cash'))");
        } else {
            // Para MySQL
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->enum('type', ['paypal', 'binance_pay', 'pago_movil', 'stripe', 'wepay', 'cash'])->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar registros con type = 'cash' antes de revertir
        DB::table('payment_methods')->where('type', 'cash')->delete();
        
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE payment_methods DROP CONSTRAINT IF EXISTS payment_methods_type_check");
            DB::statement("ALTER TABLE payment_methods ADD CONSTRAINT payment_methods_type_check CHECK (type IN ('paypal', 'binance_pay', 'pago_movil', 'stripe', 'wepay'))");
        } else {
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->enum('type', ['paypal', 'binance_pay', 'pago_movil', 'stripe', 'wepay'])->change();
            });
        }
    }
};
