<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The nurses table stores nurse/assistant/receptionist-specific data.
     * Each nurse is linked 1:1 to a user via user_id.
     */
    public function up(): void
    {
        Schema::create('nurses', function (Blueprint $table) {
            $table->id();

            // 1:1 relationship with users table.
            // unique() ensures that one user can only be one nurse.
            $table->foreignId('user_id')
                ->constrained()
                ->unique()
                ->cascadeOnDelete();

            // Basic identity details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();

            // Role in the clinic/consulting room context
            $table->string('position', 100)->nullable(); // e.g. "assistant", "receptionist"

            // Contact info
            $table->string('phone')->nullable();
            $table->string('secondary_phone')->nullable();

            // Optional professional fields
            $table->string('license_number')->nullable(); // if some nurses have a license/certification

            // General notes
            $table->text('notes')->nullable();

            // Status field
            $table->string('status', 50)->default('active'); // "active", "inactive", etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
