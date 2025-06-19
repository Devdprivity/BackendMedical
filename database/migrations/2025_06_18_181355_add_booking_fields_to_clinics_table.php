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
        Schema::table('clinics', function (Blueprint $table) {
            $table->string('booking_slug')->unique()->nullable()->after('description');
            $table->boolean('booking_enabled')->default(true)->after('booking_slug');
            $table->boolean('has_multiple_locations')->default(false)->after('booking_enabled');
            $table->string('website')->nullable()->after('email');
            
            $table->index('booking_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropIndex(['booking_slug']);
            $table->dropColumn([
                'booking_slug',
                'booking_enabled',
                'has_multiple_locations',
                'website'
            ]);
        });
    }
};
