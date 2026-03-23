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
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');

            // Ubicación
            $table->string('city', 100);
            $table->text('address')->nullable();
            $table->string('location_name', 200)->nullable(); // "Hospital Central", "Consultorio Privado"

            // Tipo de programación
            $table->enum('schedule_type', ['daily', 'weekly', 'monthly', 'specific_date'])->default('weekly');
            $table->date('specific_date')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Días de la semana
            $table->boolean('monday')->default(false);
            $table->boolean('tuesday')->default(false);
            $table->boolean('wednesday')->default(false);
            $table->boolean('thursday')->default(false);
            $table->boolean('friday')->default(false);
            $table->boolean('saturday')->default(false);
            $table->boolean('sunday')->default(false);

            // Horarios en formato JSON
            // Ejemplo: [{"start": "08:00", "end": "12:00"}, {"start": "14:00", "end": "18:00"}]
            $table->json('time_slots');
            $table->integer('appointment_duration')->default(30); // minutos por cita

            // Estado
            $table->enum('status', ['active', 'inactive', 'temporary'])->default('active');
            $table->boolean('is_available_for_booking')->default(true);

            // Notas
            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['doctor_id', 'start_date', 'end_date']);
            $table->index('city');
            $table->index('specific_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
