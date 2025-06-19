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
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('payment_status', ['not_required', 'pending', 'paid', 'failed', 'refunded'])
                  ->default('not_required')
                  ->after('status');
            $table->decimal('consultation_fee', 10, 2)->nullable()->after('payment_status');
            $table->string('payment_currency', 3)->nullable()->after('consultation_fee');
            
            // Índice para búsquedas por estado de pago
            $table->index(['payment_status', 'date_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'date_time']);
            $table->dropColumn(['payment_status', 'consultation_fee', 'payment_currency']);
        });
    }
};
