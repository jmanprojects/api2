<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The pre_consultations table stores triage/vital signs
     * taken before the main medical consultation.
     */
    public function up(): void
    {
        Schema::create('pre_consultations', function (Blueprint $table) {
            $table->id();

            // 1:1 with appointment - one preconsultation per appointment
            $table->foreignId('appointment_id')
                ->constrained()
                ->unique()
                ->cascadeOnDelete();

            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            // Nurse who performed the triage (nullable if done by the doctor)
            $table->foreignId('nurse_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Vital signs
            $table->decimal('height', 5, 2)->nullable();                   // in cm or m, depending on convention
            $table->decimal('weight', 5, 2)->nullable();                   // in kg
            $table->decimal('temperature', 4, 1)->nullable();              // in °C
            $table->unsignedSmallInteger('blood_pressure_systolic')->nullable();
            $table->unsignedSmallInteger('blood_pressure_diastolic')->nullable();
            $table->unsignedSmallInteger('heart_rate')->nullable();       // bpm
            $table->unsignedSmallInteger('respiratory_rate')->nullable(); // breaths per minute
            $table->unsignedSmallInteger('oxygen_saturation')->nullable(); // %

            // Optional additional measurements
            $table->decimal('blood_glucose', 6, 2)->nullable();            // mg/dL
            $table->unsignedTinyInteger('pain_scale')->nullable();        // 0-10

            // General notes from triage
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['doctor_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_consultations');
    }
};
