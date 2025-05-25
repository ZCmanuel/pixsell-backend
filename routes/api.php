<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
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

Route::middleware([IsUserAuth::class, IsAdmin::class])->group(function () {
    Route::get('admin/users', [UsersController::class, 'users']); // Obtiene todos los usuarios
    Route::get('admin/users/{id}', [UsersController::class, 'show']); // Obtiene un usuario por ID
    Route::put('admin/users/{id}', [UsersController::class, 'update']); // Actualiza un usuario
});

// ----------- RUTAS ADMINISTRADOR --------------
// Route::middleware([IsAdmin::class])->group(function () {
//     Route::get('admin/users', [AuthController::class, 'users']); // Obtiene todos los usuarios
// });