<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The doctor_working_hours table defines weekly recurring
     * working hours per doctor and consulting room.
     */
    public function up(): void
    {
        Schema::create('doctor_working_hours', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('consulting_room_id')
                ->constrained()
                ->cascadeOnDelete();

            // weekday: 1 (Monday) ... 7 (Sunday) or similar convention
            $table->unsignedTinyInteger('weekday'); // 1=Monday ... 7=Sunday

            // Daily start and end times for that weekday
            $table->time('start_time');
            $table->time('end_time');

            // Slot duration in minutes (e.g. 15, 20, 30)
            $table->unsignedInteger('slot_duration_minutes')->default(20);

            $table->timestamps();

            $table->unique([
                'doctor_id',
                'consulting_room_id',
                'weekday',
                'start_time',
                'end_time',
            ], 'doctor_room_weekday_time_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_working_hours');
    }
};
