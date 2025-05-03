<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']); // Obtiene el usuario autenticado
    Route::post('/logout', [AuthController::class, 'logout']); // Cierra la sesión del usuario
});