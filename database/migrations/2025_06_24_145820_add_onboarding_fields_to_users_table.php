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
            // Onboarding tracking
            $table->boolean('onboarding_completed')->default(false);
            $table->boolean('onboarding_profile_completed')->default(false);
            $table->boolean('onboarding_schedule_completed')->default(false);
            $table->boolean('onboarding_booking_completed')->default(false);
            $table->boolean('onboarding_payments_completed')->default(false);
            $table->timestamp('onboarding_completed_at')->nullable();
            
            // Profile fields (if not already exist)
            if (!Schema::hasColumn('users', 'medical_license')) {
                $table->string('medical_license')->nullable();
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable();
            }
            if (!Schema::hasColumn('users', 'consultation_fee')) {
                $table->decimal('consultation_fee', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'years_experience')) {
                $table->integer('years_experience')->nullable();
            }
            
            // Payment methods
            if (!Schema::hasColumn('users', 'payment_methods')) {
                $table->json('payment_methods')->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_account')) {
                $table->string('bank_account')->nullable();
            }
            if (!Schema::hasColumn('users', 'paypal_email')) {
                $table->string('paypal_email')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove onboarding tracking fields
            $table->dropColumn([
                'onboarding_completed',
                'onboarding_profile_completed', 
                'onboarding_schedule_completed',
                'onboarding_booking_completed',
                'onboarding_payments_completed',
                'onboarding_completed_at'
            ]);
            
            // Remove profile fields if they were added by this migration
            if (Schema::hasColumn('users', 'medical_license')) {
                $table->dropColumn('medical_license');
            }
            if (Schema::hasColumn('users', 'bio')) {
                $table->dropColumn('bio');
            }
            if (Schema::hasColumn('users', 'consultation_fee')) {
                $table->dropColumn('consultation_fee');
            }
            if (Schema::hasColumn('users', 'years_experience')) {
                $table->dropColumn('years_experience');
            }
            
            // Remove payment fields
            if (Schema::hasColumn('users', 'payment_methods')) {
                $table->dropColumn('payment_methods');
            }
            if (Schema::hasColumn('users', 'bank_account')) {
                $table->dropColumn('bank_account');
            }
            if (Schema::hasColumn('users', 'paypal_email')) {
                $table->dropColumn('paypal_email');
            }
        });
    }
};
