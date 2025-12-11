<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The consultations table stores the core medical encounter/visit
     * linked to an appointment.
     */
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();

            // 1:1 with appointment - one main consultation per appointment
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

            // Optional link to preconsultation record
            $table->foreignId('pre_consultation_id')
                ->nullable()
                ->constrained('pre_consultations')
                ->nullOnDelete();

            // Timestamps for consultation process
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();

            // Clinical fields (basic SOAP-like structure)
            $table->text('chief_complaint')->nullable();         // main reason for visit
            $table->text('subjective')->nullable();              // patient's history / symptoms
            $table->text('objective')->nullable();               // physical exam and findings
            $table->text('assessment')->nullable();              // clinical impression / diagnosis
            $table->text('plan')->nullable();                    // treatment plan, recommendations

            // ICD or custom diagnosis codes could be added later in separate tables.

            $table->text('notes')->nullable();                   // additional notes

            $table->timestamps();

            $table->index(['doctor_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
