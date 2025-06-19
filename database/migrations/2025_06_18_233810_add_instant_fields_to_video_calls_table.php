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
        Schema::table('video_calls', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('appointment_id');
            $table->boolean('is_instant')->default(false)->after('recording_enabled');
            
            // Add foreign key constraint for created_by
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Make appointment_id nullable for instant calls
            $table->unsignedBigInteger('appointment_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_calls', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by', 'is_instant']);
            
            // Revert appointment_id to not nullable
            $table->unsignedBigInteger('appointment_id')->nullable(false)->change();
        });
    }
};
