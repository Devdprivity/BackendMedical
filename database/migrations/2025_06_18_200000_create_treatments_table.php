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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('clinic_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['medication', 'therapy', 'procedure', 'lifestyle', 'diet', 'exercise', 'follow_up']);
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->text('instructions');
            $table->json('medications')->nullable(); // Array de medicamentos con dosificación
            $table->text('precautions')->nullable();
            $table->text('side_effects_to_watch')->nullable();
            $table->enum('status', ['active', 'completed', 'suspended', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->json('shared_via')->nullable(); // Registro de como se compartió (email, whatsapp, qr)
            $table->timestamp('last_shared_at')->nullable();
            $table->string('qr_code')->nullable(); // Hash único para QR
            $table->timestamps();
            
            $table->index(['patient_id', 'doctor_id']);
            $table->index(['status', 'start_date']);
            $table->index('qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
}; 