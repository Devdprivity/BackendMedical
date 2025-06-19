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
            $table->string('confirmation_token')->nullable()->after('status');
            $table->timestamp('confirmed_at')->nullable()->after('confirmation_token');
            $table->date('appointment_date')->nullable()->after('confirmed_at');
            $table->time('appointment_time')->nullable()->after('appointment_date');
            
            $table->index('confirmation_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['confirmation_token']);
            
            $table->dropColumn([
                'confirmation_token',
                'confirmed_at',
                'appointment_date',
                'appointment_time'
            ]);
        });
    }
};
