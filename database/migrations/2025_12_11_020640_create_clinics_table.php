<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The clinics table represents organizations such as hospitals,
     * medical centers or private clinics that group doctors and consulting rooms.
     */
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();

            // Basic identification
            $table->string('name');                     // public/commercial name
            $table->string('legal_name')->nullable();   // legal entity name if different
            $table->string('tax_id')->nullable();       // RFC or similar, optional

            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Address - we keep it simple but structured enough
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country', 100)->nullable();

            // Status to enable/disable the clinic without deleting it
            $table->string('status', 50)->default('active'); // e.g. "active", "inactive"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the clinics table.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
