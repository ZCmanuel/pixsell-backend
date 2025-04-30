<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/YYYY_MM_DD_create_selecciones_table.php
    public function up()
    {
        Schema::create('selecciones', function (Blueprint $table) {
            $table->id('id_seleccion');  // Clave primaria
            $table->unsignedBigInteger('id_multimedia');  // Relación con la tabla multimedia
            $table->unsignedBigInteger('id_album');  // Relación con la tabla albumes
            $table->foreign('id_multimedia')->references('id_multimedia')->on('multimedia');  // Clave foránea hacia multimedia
            $table->foreign('id_album')->references('id_album')->on('albumes');  // Clave foránea hacia albumes
            $table->unique(['id_multimedia', 'id_album']);  // Evita que una foto se seleccione más de una vez en el mismo álbum
            $table->timestamps();  // Created at, updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('selecciones');
    }
};
