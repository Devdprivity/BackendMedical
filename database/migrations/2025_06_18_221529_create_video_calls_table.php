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
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->string('room_name')->unique();
            $table->string('room_url');
            $table->string('status')->default('pending'); // pending, active, completed, cancelled
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->json('participants')->nullable(); // Track who joined
            $table->text('notes')->nullable();
            $table->boolean('recording_enabled')->default(false);
            $table->string('recording_url')->nullable();
            $table->timestamps();
            
            $table->index(['appointment_id', 'status']);
            $table->index('room_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_calls');
    }
};
