<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsersController
{
    /**
     * Lista todos los usuarios con paginación y filtro por nombre.
     * ENDPOINT: /api/admin/users
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function users(Request $request)
    {
        $query = Usuario::query();

        // Filtro por nombre (si existe)
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        // Paginación
        $perPage = $request->input('per_page', 10);
        $usuarios = $query->paginate($perPage);

        return response()->json([
            'users' => $usuarios->items(),
            'meta' => [
                'current_page' => $usuarios->currentPage(),
                'last_page' => $usuarios->lastPage(),
                'per_page' => $usuarios->perPage(),
                'total' => $usuarios->total(),
            ],
        ]);

    }

    /**
     * Muestra un usuario específico por ID.
     * ENDPOINT: /api/admin/users/{id}
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }


    /**
     * Actualiza un usuario específico por ID.
     * ENDPOINT: /api/admin/users/{id}
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'nombre' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:usuarios,email,' . $id . ',id_usuario',
                'contraseña' => 'sometimes|string|min:10',
                'rol' => 'sometimes|string|in:fotógrafo,cliente',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Actualizar los campos del usuario
        if (isset($validatedData['contraseña'])) {
            $validatedData['contraseña'] = bcrypt($validatedData['contraseña']);
        }

        $usuario->update($validatedData);

        return response()->json(['message' => 'Usuario actualizado correctamente', 'usuario' => $usuario], 200);
    }

    /**
     * Actualiza los datos del usuario autenticado.
     * ENDPOINT: /api/user/update
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMe(Request $request)
    {
        $usuario = auth()->user(); // Obtener el usuario autenticado

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'nombre' => 'sometimes|string|max:255',
                'contraseña_actual' => 'required_with:contraseña|string|min:10', // Requerida si se envía una nueva contraseña
                'contraseña' => 'sometimes|string|min:10|confirmed', // Nueva contraseña debe ser confirmada
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Validar la contraseña actual antes de permitir el cambio de contraseña
        if (isset($validatedData['contraseña']) && !\Hash::check($validatedData['contraseña_actual'], $usuario->contraseña)) {
            return response()->json(['message' => 'La contraseña actual no es correcta'], 403);
        }

        // Actualizar los campos del usuario
        $updateData = [];
        if (isset($validatedData['nombre'])) {
            $updateData['nombre'] = $validatedData['nombre'];
        }
        if (isset($validatedData['contraseña'])) {
            $updateData['contraseña'] = bcrypt($validatedData['contraseña']);
        }

        $usuario->update($updateData);

        return response()->json(['message' => 'Usuario actualizado correctamente', 'usuario' => $usuario], 200);
    }
}
