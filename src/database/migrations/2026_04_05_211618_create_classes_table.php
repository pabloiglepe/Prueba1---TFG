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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();

            // RELACIÓN CON EL ENTRENADOR Y LA PISTA 
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');

            $table->string('title', 50);
            $table->enum('type', ['individual', 'group']);
            $table->enum('level', ['initiation', 'intermediate', 'advanced']);
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->enum('status', ['registered', 'cancelled', 'completed'])->default('registered');

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            $table->tinyInteger('max_players')->unsigned();
            $table->decimal('price', 8, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
