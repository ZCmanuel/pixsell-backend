<?php

use App\Http\Controllers\AlbumsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;

// ENDPOINTS DE AUTENTICACIÓN
Route::post('/register', [AuthController::class, 'register']); // Registra un nuevo usuario
Route::post('/login', [AuthController::class, 'login']);


// ----------- RUTAS PROTEGIDAS --------------
Route::middleware([IsUserAuth::class])->group(function () {
    Route::get('/me', [AuthController::class, 'me']); // Obtiene el usuario autenticado
    Route::post('/logout', [AuthController::class, 'logout']); // Cierra la sesión del usuario
    Route::put('/user/update', [UsersController::class, 'updateMe']); // Actualiza el usuario autenticado
});

// ----------- RUTAS DE ADMIN --------------
Route::middleware([IsUserAuth::class, IsAdmin::class])->group(function () {
    // ENDPOINTS DE USUARIOS
    Route::get('admin/users', [UsersController::class, 'users']); // Obtiene todos los usuarios
    Route::get('admin/users/{id}', [UsersController::class, 'show']); // Obtiene un usuario por ID
    Route::put('admin/users/{id}', [UsersController::class, 'update']); // Actualiza un usuario

    // ENDPOINTS DE ALBUMES
    Route::get('admin/albums', [AlbumsController::class, 'albums']); // Obtiene todos los usuarios
    Route::post('admin/albums', [AlbumsController::class, 'createAlbum']); // Crea un nuevo álbum
    Route::get('/admin/albums/{id_usuario}', [AlbumsController::class, 'getAlbumsByUser']);

    // ENDPOINTS DE ESTADÍSTICAS
    Route::get('admin/estadisticas/albums', [DataController::class, 'albumEstads']); // Obtiene estadísticas de álbumes
    Route::get('admin/estadisticas/users', [DataController::class, 'usersEstads']); // Obtiene estadísticas de usuarios
});

