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
        Schema::create('doctor_patient_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('clinic_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('relationship_type', ['primary', 'consulting', 'specialist', 'emergency'])->default('primary');
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'transferred'])->default('active');
            $table->text('notes')->nullable();
            $table->json('permissions')->nullable(); // Permisos específicos (view_history, prescribe, etc.)
            $table->timestamps();
            
            // Indexes and constraints
            $table->index(['doctor_id', 'patient_id']);
            $table->index(['patient_id', 'doctor_id']);
            $table->index('relationship_type');
            $table->index('status');
            $table->index('started_at');
            
            // Unique constraint with shorter name
            $table->unique(['doctor_id', 'patient_id', 'relationship_type'], 'dr_patient_rel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_patient_relationships');
    }
}; 