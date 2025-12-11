<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Pivot table linking doctors to consulting rooms.
     * This allows:
     *  - one doctor to work in multiple consulting rooms
     *  - one consulting room to host multiple doctors (e.g. different shifts)
     */
    public function up(): void
    {
        Schema::create('consulting_room_doctor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consulting_room_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            // Mark if this consulting room is the primary one for the doctor
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            // Avoid duplicate assignments (same doctor in same room multiple times)
            $table->unique(['consulting_room_id', 'doctor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the pivot table.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulting_room_doctor');
    }
};
