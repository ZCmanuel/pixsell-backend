<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ENDPOINTS DE AUTENTICACIÓN
Route::post('/register', [AuthController::class, 'register']); // Registra un nuevo usuario
Route::post('/login', [AuthController::class, 'login']);


// ----------- RUTAS PROTEGIDAS --------------
Route::middleware([IsUserAuth::class])->group(function () {
    Route::get('/me', [AuthController::class, 'me']); // Obtiene el usuario autenticado
    Route::post('/logout', [AuthController::class, 'logout']); // Cierra la sesión del usuario
});

// ----------- RUTAS ADMINISTRADOR --------------
Route::middleware([IsAdmin::class])->group(function () {

});