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

    // ENDPOINTS DE ALBUMES
    Route::get('/user/albums', [AlbumsController::class, 'getUserAlbums']); // Lista los álbumes del usuario autenticado
    Route::get('/album/{id}', [AlbumsController::class, 'getAlbumById']); // Obtiene albumes por id
    Route::post('/albums/seleccion/{id_album}', [AlbumsController::class, 'selectImages']);

    // ENDPONTS ESTADÍSTICAS
    route::get('/estadisticas/albums', [DataController::class, 'userAlbumStats']); // Obtiene estadísticas de álbumes
});

// ----------- RUTAS DE ADMIN --------------
Route::middleware([IsUserAuth::class, IsAdmin::class])->group(function () {
    // ENDPOINTS DE USUARIOS
    Route::get('admin/users', [UsersController::class, 'users']); // Obtiene todos los usuarios
    Route::get('admin/users/{id}', [UsersController::class, 'show']); // Obtiene un usuario por ID
    Route::put('admin/users/{id}', [UsersController::class, 'update']); // Actualiza un usuario

    // ENDPOINTS DE ALBUMES
    Route::get('admin/albums', action: [AlbumsController::class, 'albums']); // Obtiene todos los albumes
    Route::post('admin/albums', [AlbumsController::class, 'createAlbum']); // Crea un nuevo álbum
    Route::get('admin/albums/{id_usuario}', [AlbumsController::class, 'getAlbumsByUser']); // Obtiene álbumes por ID de usuario
    Route::patch('/albums/{id_album}/finalize', [AlbumsController::class, 'finalizeAlbum']);

    // ENDPOINTS DE ESTADÍSTICAS
    Route::get('admin/estadisticas/albums', [DataController::class, 'albumEstads']); // Obtiene estadísticas de álbumes
    Route::get('admin/estadisticas/users', [DataController::class, 'usersEstads']); // Obtiene estadísticas de usuarios
});

