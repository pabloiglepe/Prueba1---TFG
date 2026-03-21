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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // RELACIÓN CON ROLES Y PISTAS 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');


            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');

            
            $table->decimal('total_price', 8, 2);
            $table->enum('status', ['pendiente', 'pagada', 'cancelada'])->default('pendiente');
            $table->string('notes', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
