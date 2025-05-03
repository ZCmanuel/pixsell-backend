<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Autentica al usuario y genera un token JWT, de lo contrario, retorna un error 401 Unauthorized.
     * 
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
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        /** @var \App\Models\Usuario $user */
        $user = auth()->user();
        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Cierra la sesión del usuario autenticado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth();
        $auth->logout();
        return response()->json(['message' => 'Sesión cerrada']);
    }
}
