<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The prescriptions table stores prescription headers
     * linked to a consultation (and therefore to an appointment).
     */
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consultation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete();

            // When the prescription was issued
            $table->dateTime('issued_at');

            // Optional general notes (e.g. indications not tied to a specific medicine)
            $table->text('notes')->nullable();

            // If you ever want to version/duplicate a prescription, you can add fields later

            $table->timestamps();

            $table->index(['doctor_id', 'patient_id', 'issued_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
