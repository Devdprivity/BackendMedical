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
        Schema::create('clinic_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->string('name');
            $table->text('address');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->time('schedule_start')->default('08:00');
            $table->time('schedule_end')->default('17:00');
            $table->json('work_days')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->index('clinic_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_locations');
    }
};
