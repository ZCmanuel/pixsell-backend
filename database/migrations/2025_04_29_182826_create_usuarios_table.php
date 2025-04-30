<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/YYYY_MM_DD_create_usuarios_table.php
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');  // Clave primaria 
            $table->string('nombre', 100); // Nombre del usuario
            $table->string('email')->unique(); // Email único
            $table->string('contraseña'); // Contraseña encriptada
            $table->enum('rol', ['fotógrafo', 'cliente'])->default('cliente'); // Rol del usuario
            $table->timestamps();  // Created at, updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};
