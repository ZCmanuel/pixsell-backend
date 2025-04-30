<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/YYYY_MM_DD_create_multimedia_table.php
    public function up()
    {
        Schema::create('multimedia', function (Blueprint $table) {
            $table->id('id_multimedia');  // Clave primaria
            $table->unsignedBigInteger('id_album');  // Relación con la tabla albumes
            $table->string('ruta_archivo');
            $table->enum('tipo', ['foto', 'video']);
            $table->foreign('id_album')->references('id_album')->on('albumes');  // Clave foránea hacia albumes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('multimedia');
    }
};
