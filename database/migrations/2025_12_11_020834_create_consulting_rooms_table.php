<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The consulting_rooms table represents the physical rooms or spaces
     * where doctors see patients. A consulting room can optionally belong
     * to a clinic, or be independent (clinic_id nullable).
     */
    public function up(): void
    {
        Schema::create('consulting_rooms', function (Blueprint $table) {
            $table->id();

            // Optional link to a clinic.
            // If clinic_id is null, this is an independent/private consulting room.
            $table->foreignId('clinic_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Basic identification
            $table->string('name');                    // e.g. "Consulting Room 201", "Main Office"
            $table->string('code')->nullable();        // optional internal code

            // Description and location details
            $table->text('description')->nullable();
            $table->string('floor')->nullable();       // e.g. "2nd floor"
            $table->string('room_number')->nullable(); // e.g. "201"

            // Address fields - useful for standalone offices
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country', 100)->nullable();

            $table->string('phone')->nullable();

            // Status for enabling/disabling the consulting room
            $table->string('status', 50)->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the consulting_rooms table.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulting_rooms');
    }
};
