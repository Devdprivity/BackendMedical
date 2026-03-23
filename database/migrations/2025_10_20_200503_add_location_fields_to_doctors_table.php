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
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('base_city', 100)->nullable()->after('rating');
            $table->foreignId('base_clinic_id')->nullable()->after('base_city')->constrained('clinics')->onDelete('set null');
            $table->boolean('travels_to_locations')->default(false)->after('base_clinic_id');
            $table->json('preferred_cities')->nullable()->after('travels_to_locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['base_clinic_id']);
            $table->dropColumn(['base_city', 'base_clinic_id', 'travels_to_locations', 'preferred_cities']);
        });
    }
};
