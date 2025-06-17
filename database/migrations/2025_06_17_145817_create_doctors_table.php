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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('specialty');
            $table->string('license_number')->unique(); // CMP
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('emergency_phone')->nullable();
            $table->text('address');
            $table->integer('experience_years');
            $table->json('education'); // Array de educación
            $table->json('certifications'); // Array de certificaciones
            $table->json('languages'); // Array de idiomas
            $table->enum('status', ['active', 'inactive', 'vacation', 'leave'])->default('active');
            $table->text('bio')->nullable();
            $table->string('photo_url')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
