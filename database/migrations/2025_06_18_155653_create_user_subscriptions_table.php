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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            
            // Subscription status
            $table->enum('status', ['active', 'cancelled', 'expired', 'trial'])->default('trial');
            
            // Dates
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Payment info
            $table->string('payment_method')->nullable(); // stripe, paypal, etc.
            $table->string('payment_id')->nullable(); // external payment ID
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            
            // Usage tracking
            $table->integer('current_doctors_count')->default(0);
            $table->integer('current_patients_count')->default(0);
            $table->integer('current_appointments_this_month')->default(0);
            $table->integer('current_locations_count')->default(0);
            $table->integer('current_staff_count')->default(0);
            
            // Reset counters
            $table->date('last_monthly_reset')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
