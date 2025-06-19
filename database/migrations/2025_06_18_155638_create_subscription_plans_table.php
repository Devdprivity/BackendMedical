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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->integer('trial_days')->default(0);
            
            // Limits
            $table->integer('max_doctors')->nullable(); // null = unlimited
            $table->integer('max_patients')->nullable(); // null = unlimited
            $table->integer('max_appointments_per_month')->nullable(); // null = unlimited
            $table->integer('max_locations')->nullable(); // null = unlimited
            $table->integer('max_staff')->nullable(); // null = unlimited
            
            // Features (JSON)
            $table->json('features'); // Array of feature slugs
            
            // Display order
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
