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
        Schema::create('classes_reservations', function (Blueprint $table) {
            $table->id();

            // RELACIÓN CON LA CLASE Y EL ALUMNO 
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('status', ['registered', 'cancelled'])->default('registered');

            // EVITAR QUE UN ALUMNO SE INSCRIBA DOS VECES A LA MISMA CLASE
            $table->unique(['class_id', 'user_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes_reservations');
    }
};
