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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->decimal('weight', 5, 2)->nullable(); // Peso en kg
            $table->decimal('height', 5, 2)->nullable(); // Altura en cm
            $table->string('blood_pressure')->nullable(); // Presión arterial
            $table->integer('heart_rate')->nullable(); // Ritmo cardíaco
            $table->decimal('temperature', 4, 2)->nullable(); // Temperatura en °C
            $table->datetime('measured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
