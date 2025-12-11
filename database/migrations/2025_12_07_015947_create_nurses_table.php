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
        Schema::create('nurses', function (Blueprint $table) {
            $table->id();

            // Relación con users: cuenta de acceso del enfermero/recepcionista
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Relación con doctors: con qué doctor trabaja
            $table->foreignId('doctor_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Datos básicos del perfil (los podrá llenar después)
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('position')->nullable(); // ej. 'Enfermero', 'Recepcionista'

            // Por si en algún momento quieres activar/desactivar acceso
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
