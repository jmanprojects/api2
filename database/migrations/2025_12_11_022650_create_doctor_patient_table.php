<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Pivot table that represents the clinical relationship
     * between doctors and patients.
     */
    public function up(): void
    {
        Schema::create('doctor_patient', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete();

            // Useful metadata for the relationship
            $table->dateTime('first_seen_at')->nullable();  // first appointment/consultation date
            $table->dateTime('last_seen_at')->nullable();   // last appointment/consultation date
            $table->string('status', 50)->default('active'); // "active", "inactive", "transferred", etc.
            $table->text('notes')->nullable();               // general notes for this doctor-patient relationship

            $table->timestamps();

            // Avoid duplicate pairs of doctor-patient
            $table->unique(['doctor_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_patient');
    }
};
