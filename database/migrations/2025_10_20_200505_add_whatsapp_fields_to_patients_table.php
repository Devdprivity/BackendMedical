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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('whatsapp_number', 20)->nullable()->after('phone');
            $table->boolean('whatsapp_opt_in')->default(false)->after('whatsapp_number');
            $table->boolean('prefers_whatsapp')->default(false)->after('whatsapp_opt_in');
            $table->datetime('whatsapp_opt_in_date')->nullable()->after('prefers_whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'whatsapp_opt_in', 'prefers_whatsapp', 'whatsapp_opt_in_date']);
        });
    }
};
