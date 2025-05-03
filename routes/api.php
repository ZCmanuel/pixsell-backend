<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ENDPOINTS DE AUTENTICACIÓN
// Ruta para registrar un nuevo usuario
Route::post('/login', [AuthController::class, 'login']);

// ----------- RUTAS PROTEGIDAS --------------
// Ruta para obtener la información del usuario autenticado -> Token JWT 
Route::get('/me', [AuthController::class, 'me']); // Obtiene el usuario autenticado
// Ruta para cerrar sesión -> Token JWT
Route::post('/logout', [AuthController::class, 'logout']); // Cierra la sesión del usuario

