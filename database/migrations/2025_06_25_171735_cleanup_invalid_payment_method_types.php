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
        // Limpiar tipos de payment methods inválidos
        // Convertir 'transfer' a 'pago_movil' (más apropiado para transferencias)
        DB::table('payment_methods')
            ->where('type', 'transfer')
            ->update([
                'type' => 'pago_movil',
                'instructions' => 'Pago vía transferencia bancaria o pago móvil'
            ]);

        // Convertir 'card' a 'stripe' (más apropiado para pagos con tarjeta)
        DB::table('payment_methods')
            ->where('type', 'card')
            ->update([
                'type' => 'stripe',
                'instructions' => 'Pago con tarjeta de crédito/débito vía Stripe'
            ]);

        // Eliminar cualquier otro tipo inválido que no esté en el enum
        $validTypes = ['paypal', 'binance_pay', 'pago_movil', 'stripe', 'wepay', 'cash'];
        DB::table('payment_methods')
            ->whereNotIn('type', $validTypes)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No necesitamos revertir esta limpieza
        // Los datos ya están normalizados
    }
};
