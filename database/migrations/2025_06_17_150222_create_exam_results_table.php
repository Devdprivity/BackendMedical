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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_exam_id')->constrained()->onDelete('cascade');
            $table->datetime('performed_date');
            $table->foreignId('reported_by')->constrained('doctors');
            $table->json('values'); // Array de parámetros y resultados medidos
            $table->json('reference_values')->nullable(); // Valores de referencia
            $table->text('interpretation')->nullable();
            $table->json('attachments')->nullable(); // Array de archivos adjuntos
            $table->enum('status', ['pending', 'reviewed', 'approved', 'requires_attention'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
