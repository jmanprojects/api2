<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The patients table stores patient-specific data.
     * Each patient is linked 1:1 to a user via user_id.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // 1:1 relationship with users table.
            // unique() ensures that one user can only be one patient.
            $table->foreignId('user_id')
                ->constrained()
                ->unique()
                ->cascadeOnDelete();

            // Basic identity details (more structured than the generic "name" in users)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable(); // optional second last name

            // Demographic data
            $table->string('gender', 20)->nullable();      // "male", "female", "other", etc.
            $table->date('birth_date')->nullable();
            $table->string('marital_status', 50)->nullable(); // "single", "married", etc.
            $table->string('occupation', 100)->nullable();

            // Contact info (can differ from user's main email)
            $table->string('phone')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->string('alternate_email')->nullable();

            // Identification
            $table->string('document_type', 50)->nullable();  // "INE", "passport", etc.
            $table->string('document_number', 100)->nullable();

            // Basic clinical info (lightweight, more detailed info will live in other tables)
            $table->string('blood_type', 10)->nullable();     // "O+", "A-", etc.
            $table->text('allergies')->nullable();            // short notes about allergies
            $table->text('chronic_conditions')->nullable();   // e.g. "diabetes, hypertension"

            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // General notes about patient (non-structured)
            $table->text('notes')->nullable();

            // Status to enable/disable patient without deleting
            $table->string('status', 50)->default('active');  // "active", "inactive", etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
