<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /**
     * Autentica al usuario y genera un token JWT, de lo contrario, retorna un error 401 Unauthorized.
     *  ENDPOINT: /api/login
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'contraseña' => 'required|string',
        ]);

        if (! $token = JWTAuth::attempt(['email' => $credentials['email'], 'password' => $credentials['contraseña']])) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return response()->json(['token' => $token]);
    }
    /**
     * Retorna la información del usuario autenticado (JWT).
     * ENDPOINT: /api/me
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
        return response()->json([
            'user' => [
                'id' => $user->id_usuario,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
            ],
            'message' => 'Usuario autenticado correctamente',
        ],200);
    }

    /**
     * Cierra la sesión del usuario autenticado.
     * ENDPOINT: /api/logout
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Sesión cerrada'], 200);
    }
}
