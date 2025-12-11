<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The appointment_status_histories table keeps a log
     * of every status change for an appointment.
     */
    public function up(): void
    {
        Schema::create('appointment_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')
                ->constrained()
                ->cascadeOnDelete();

            // Previous and new status values
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50);

            // Who made the change
            $table->foreignId('changed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Optional reason or comment about this transition
            $table->string('reason')->nullable();

            // When the change occurred
            $table->dateTime('changed_at');

            $table->timestamps();

            $table->index(['appointment_id', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_status_histories');
    }
};
