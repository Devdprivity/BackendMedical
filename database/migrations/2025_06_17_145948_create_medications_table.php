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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->onDelete('set null');
            $table->string('commercial_name');
            $table->string('generic_name');
            $table->string('manufacturer');
            $table->string('barcode')->nullable()->unique();
            $table->string('category');
            $table->string('presentation');
            $table->string('concentration');
            $table->string('administration_route');
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('controlled')->default(false);
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(0);
            $table->string('location')->nullable();
            $table->date('expiration_date');
            $table->string('lot_number');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->enum('status', ['active', 'discontinued', 'out_of_stock'])->default('active');
            $table->text('indications')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('drug_interactions')->nullable();
            $table->text('storage_notes')->nullable();
            $table->timestamps();
            
            $table->index(['created_by', 'status']);
            $table->index(['clinic_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
