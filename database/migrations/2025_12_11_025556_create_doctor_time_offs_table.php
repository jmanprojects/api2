<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The doctor_time_offs table stores exceptions to the regular
     * schedule (vacations, conferences, personal leave, etc.).
     */
    public function up(): void
    {
        Schema::create('doctor_time_offs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            // Optional consulting room scope; null means "all rooms"
            $table->foreignId('consulting_room_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Time off range
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');

            $table->string('reason')->nullable();

            $table->timestamps();

            $table->index(['doctor_id', 'start_datetime', 'end_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_time_offs');
    }
};
