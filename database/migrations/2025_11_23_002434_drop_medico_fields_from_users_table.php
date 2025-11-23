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
            $table->dropColumn([
                'specialty',
                'phone',
                'clinic_name',
                'clinic_logo',
                'work_schedule',
                'last_name',
                'photo',
                'address',
                'cedula',
                'career',
                'university',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('specialty')->nullable();
            $table->string('phone')->nullable();
            $table->string('clinic_name')->nullable();
            $table->string('clinic_logo')->nullable();
            $table->string('work_schedule')->nullable();
            $table->string('last_name')->nullable();
            $table->string('photo')->nullable();
            $table->string('address')->nullable();
            $table->string('cedula')->nullable();
            $table->string('career')->nullable();
            $table->string('university')->nullable();
        });
    }
};
