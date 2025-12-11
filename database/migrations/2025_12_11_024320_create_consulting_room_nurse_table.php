<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Pivot table linking nurses to consulting rooms.
     * This allows:
     *  - one nurse to work in multiple consulting rooms
     *  - one consulting room to have multiple nurses
     */
    public function up(): void
    {
        Schema::create('consulting_room_nurse', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consulting_room_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('nurse_id')
                ->constrained()
                ->cascadeOnDelete();

            // Role of the nurse in that specific consulting room
            $table->string('role_in_room', 100)->nullable(); // e.g. "assistant", "receptionist"

            // Mark if this is the primary room for that nurse
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            // Avoid duplicate assignments (same nurse in same room multiple times)
            $table->unique(['consulting_room_id', 'nurse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulting_room_nurse');
    }
};
