<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * Registra un nuevo usuario en la base de datos.
     * ENDPOINT: /api/register
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3|max:100',
            'email' => 'required|string|email|min:10|max:255|unique:usuarios',
            'contraseña' => 'required|string|min:10|confirmed',
        ]);

        // Validar los datos de entrada, si no son válidos, retornar un error 422
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Verificar si el usuario ya existe en la base de datos 
        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'contraseña' => bcrypt($request->contraseña), // Encriptar la contraseña
        ]);

        // $token = JWTAuth::fromUser($usuario); // Generar un token JWT para el nuevo usuario (opcional)
        // return response()->json(['token' => $token], 201); // Retornar el token (opcional)

        return response()->json(['message' => 'Usuario registrado correctamente'], 201);
    }

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

        if (!$token = JWTAuth::attempt(['email' => $credentials['email'], 'password' => $credentials['contraseña']])) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'user' => [
                'id' => $user->id_usuario,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
            ],
            'token' => $token
        ]);
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
        ], 200);
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
