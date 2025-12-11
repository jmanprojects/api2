<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The appointments table stores scheduled visits between
     * doctors and patients in a specific consulting room.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Links to doctor, patient and consulting room
            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('consulting_room_id')
                ->constrained()
                ->cascadeOnDelete();

            // When the appointment is scheduled to start
            $table->dateTime('scheduled_at');

            // Duration in minutes (e.g. 15, 20, 30...)
            $table->unsignedInteger('duration_minutes')->default(20);

            // Type and source of the appointment
            $table->string('type', 50)->default('new');      // e.g. "new", "follow_up", "emergency"
            $table->string('source', 50)->default('manual'); // "manual", "patient_app", "nurse", etc.

            // Current status of the appointment
            $table->string('status', 50)->default('scheduled');
            // common values:
            // "scheduled", "confirmed", "cancelled", "no_show",
            // "in_preconsultation", "in_consultation", "completed"

            // Who created / cancelled the appointment
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('cancelled_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('cancelled_reason')->nullable();

            // Short description of the reason for visit
            $table->string('reason')->nullable();

            $table->timestamps();

            // Index to speed up queries by doctor and date
            $table->index(['doctor_id', 'scheduled_at']);
            $table->index(['patient_id', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
