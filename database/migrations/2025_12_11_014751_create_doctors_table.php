<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table stores doctor-specific profile information.
     * Each doctor is linked 1:1 to a user via user_id.
     */
    public function up(): void
    {
      Schema::create('doctors', function (Blueprint $table) {
          $table->id();

          // 1:1 relationship with users table.
          // unique() ensures that one user can only be one doctor.
          $table->foreignId('user_id')
              ->constrained()
              ->unique()
              ->cascadeOnDelete();

          // Professional details
          $table->string('professional_license')->nullable(); // e.g. medical license number
          $table->string('specialty')->nullable();            // main specialty (could move to a specialties table later)
          $table->string('secondary_specialty')->nullable();  // optional secondary specialty

          // Contact / personal info
          $table->string('phone')->nullable();
          $table->string('gender', 20)->nullable();           // e.g. "male", "female", "other"
          $table->date('birth_date')->nullable();

          // Presentation / profile
          $table->text('bio')->nullable();                    // short description / about the doctor
          $table->string('photo_path')->nullable();           // path or URL to doctor's profile picture

          // Status to enable/disable doctor accounts without deleting them
          $table->string('status', 50)->default('active');    // e.g. "active", "inactive", "pending"

          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the doctors table.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
