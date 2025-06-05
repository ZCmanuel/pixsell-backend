<?php

use Illuminate\Support\Facades\Route;

// Ruta para la página de bienvenida
Route::get('/', function () {
    return view('welcome');
});