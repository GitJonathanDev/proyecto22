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
        Schema::create('Encargado', function (Blueprint $table) {
            $table->unsignedBigInteger('carnetIdentidad')->primary(); 
            $table->string('nombre');
            $table->string('apellidoPaterno');
            $table->string('apellidoMaterno');
            $table->string('sexo');
            $table->integer('edad');
            $table->integer('telefono');
            $table->foreignId('codUsuarioF')->constrained('users', 'codUsuario'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Encargado');
    }
};
