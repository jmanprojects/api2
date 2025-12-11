<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The prescription_items table stores each medication or indication
     * that belongs to a prescription.
     */
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prescription_id')
                ->constrained()
                ->cascadeOnDelete();

            // Basic medicine fields - kept simple and text-based for flexibility.
            $table->string('medicine_name');       // e.g. "Ibuprofen 400mg"
            $table->string('dose')->nullable();    // e.g. "1 tablet"
            $table->string('frequency')->nullable(); // e.g. "every 8 hours"
            $table->string('duration')->nullable();  // e.g. "5 days"

            // Optional: route of administration, e.g. "oral", "IM", "IV"
            $table->string('route')->nullable();

            // Free text instructions
            $table->text('instructions')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
