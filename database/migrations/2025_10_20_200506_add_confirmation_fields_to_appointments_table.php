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
            $table->boolean('confirmation_required')->default(true)->after('status');
            $table->datetime('confirmation_sent_at')->nullable()->after('confirmation_required');
            $table->enum('confirmation_status', ['not_sent', 'pending', 'confirmed', 'no_response'])->default('not_sent')->after('confirmation_sent_at');
            $table->integer('reminder_hours_before')->default(24)->after('confirmation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['confirmation_required', 'confirmation_sent_at', 'confirmation_status', 'reminder_hours_before']);
        });
    }
};
