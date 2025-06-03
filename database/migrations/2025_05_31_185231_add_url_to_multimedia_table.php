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
        Schema::table('multimedia', function (Blueprint $table) {
            $table->string('url')->after('ruta_archivo'); // Añadir la columna 'url' después de 'ruta_archivo'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multimedia', function (Blueprint $table) {
            $table->dropColumn('url'); // Eliminar la columna 'url' si se revierte la migración
        });
    }
};
