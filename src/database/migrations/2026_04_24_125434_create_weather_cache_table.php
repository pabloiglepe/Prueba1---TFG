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
        Schema::create('weather_cache', function (Blueprint $table) {
            $table->date('date')->primary();

            // HORAS DE AMANECER Y ANOCHECER
            $table->time('sunrise');
            $table->time('sunset');
            
            // PRECIPITACIÓN PREVISTA
            $table->decimal('precipitation_mm', 5, 2)->default(0);

            // CUANDO SE OBTUVO EL DATO DE LA PETICIÓN
            $table->timestamp('fetched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_cache');
    }
};
