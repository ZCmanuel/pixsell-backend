<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('albumes', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nombre'); // Añadir la columna 'descripcion' después de 'nombre'
        });
    }

    public function down()
    {
        Schema::table('albumes', function (Blueprint $table) {
            $table->dropColumn('descripcion'); // Eliminar la columna 'descripcion' si se revierte la migración
        });
    }
};