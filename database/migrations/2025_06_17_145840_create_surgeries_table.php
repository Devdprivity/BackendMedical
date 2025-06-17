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
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('main_surgeon_id')->constrained('doctors')->onDelete('cascade');
            $table->json('assistant_surgeons')->nullable(); // Array de IDs de cirujanos asistentes
            $table->datetime('date_time');
            $table->integer('estimated_duration'); // Duración estimada en minutos
            $table->string('surgery_type');
            $table->string('operating_room');
            $table->string('anesthesia_type');
            $table->json('required_equipment')->nullable(); // Array de equipos necesarios
            $table->text('preop_notes')->nullable(); // Notas pre-operatorias
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surgeries');
    }
};
