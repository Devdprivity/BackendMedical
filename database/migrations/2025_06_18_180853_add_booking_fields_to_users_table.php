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
            $table->string('booking_slug')->unique()->nullable()->after('email');
            $table->boolean('booking_enabled')->default(false)->after('booking_slug');
            $table->decimal('consultation_fee', 8, 2)->nullable()->after('booking_enabled');
            $table->time('schedule_start')->default('08:00')->after('consultation_fee');
            $table->time('schedule_end')->default('17:00')->after('schedule_start');
            $table->json('work_days')->nullable()->after('schedule_end');
            $table->text('bio')->nullable()->after('work_days');
            $table->unsignedBigInteger('clinic_id')->nullable()->after('bio');
            $table->unsignedBigInteger('location_id')->nullable()->after('clinic_id');
            
            $table->index('booking_slug');
            $table->index('clinic_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['booking_slug']);
            $table->dropIndex(['clinic_id']);
            $table->dropIndex(['location_id']);
            
            $table->dropColumn([
                'booking_slug',
                'booking_enabled',
                'consultation_fee',
                'schedule_start',
                'schedule_end',
                'work_days',
                'bio',
                'clinic_id',
                'location_id'
            ]);
        });
    }
};
