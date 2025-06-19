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
        Schema::table('users', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('users', 'schedule_start')) {
                $table->time('schedule_start')->nullable()->after('specialty');
            }
            if (!Schema::hasColumn('users', 'schedule_end')) {
                $table->time('schedule_end')->nullable()->after('schedule_start');
            }
            if (!Schema::hasColumn('users', 'work_days')) {
                $table->json('work_days')->nullable()->after('schedule_end');
            }
            if (!Schema::hasColumn('users', 'consultation_duration')) {
                $table->integer('consultation_duration')->default(30)->after('consultation_fee');
            }
            if (!Schema::hasColumn('users', 'break_start')) {
                $table->time('break_start')->nullable()->after('work_days');
            }
            if (!Schema::hasColumn('users', 'break_end')) {
                $table->time('break_end')->nullable()->after('break_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'schedule_start', 
                'schedule_end', 
                'work_days', 
                'consultation_duration',
                'break_start',
                'break_end'
            ]);
        });
    }
};
