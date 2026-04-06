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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // RELACIÓN CON ROLES 
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');


            $table->string('name', 50);
            $table->string('email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();

            // ALGORITMO DE ENCRIPTACIÓN 'BCRYPT' PARA LAS CONTRASEÑAS
            $table->string('password', 100);

            $table->string('phone_number', 9)->unique();

            // CASILLA DE CONSENTIMIENTO DE LEY DE PROTECCIÓN DE DATOS
            $table->boolean('rgpd_consent')->default(false);

            $table->rememberToken();
            $table->timestamps();

            // PARA CUMPLIR CON LA LEY DE PROTECCIÓN DE DATOS
            $table->softDeletes();
        });


        // TABLA PARA RECUPERAR CONTRASEÑAS
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });


        // TABLA PARA GESTIONAR LAS SESIONES DE USUARIO
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }

};
