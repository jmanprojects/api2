<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('first_login')->default(true);
            $table->string('specialty')->nullable();
            $table->string('phone')->nullable();
            $table->string('clinic_name')->nullable();
            $table->string('clinic_logo')->nullable();
            // Si tu MySQL/MariaDB soporta JSON, mejor usar json:
            $table->json('work_schedule')->nullable();
            $table->json('theme')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_login',
                'specialty',
                'phone',
                'clinic_name',
                'clinic_logo',
                'work_schedule',
                'theme'
            ]);
        });
    }
};
