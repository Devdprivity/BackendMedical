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
        Schema::create('treatment_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained()->onDelete('cascade');
            $table->foreignId('medication_id')->constrained()->onDelete('cascade');
            $table->string('dosage'); // ej: "500mg"
            $table->string('frequency'); // ej: "Cada 8 horas", "3 veces al día"
            $table->string('duration'); // ej: "7 días", "2 semanas"
            $table->text('administration_instructions'); // ej: "Tomar con alimentos"
            $table->integer('quantity_prescribed'); // Cantidad total prescrita
            $table->integer('quantity_dispensed')->default(0); // Cantidad entregada
            $table->enum('status', ['pending', 'dispensed', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['treatment_id', 'status']);
            $table->index(['medication_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_medications');
    }
}; 