<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/YYYY_MM_DD_create_albumes_table.php
    public function up()
    {
        Schema::create('albumes', function (Blueprint $table) {
            $table->id('id_album');  // Clave primaria
            $table->unsignedBigInteger('id_usuario');  // Relación con la tabla usuarios
            $table->string('nombre', 150); // Nombre del álbum
            $table->enum('estado', allowed: ['pendiente', 'seleccionado', 'finalizado'])->default('pendiente'); // Estado del álbum
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');  // Clave foránea hacia usuarios
            $table->timestamps();  // Created at, updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('albumes');
    }
};
