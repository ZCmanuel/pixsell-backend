<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;

class AlbumsController
{
    /**
     * Lista todos los álbumes con paginación, filtros y ordenación.
     * ENDPOINT: /api/albums
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function albums(Request $request)
    {
        $query = Album::query();

        // Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Buscar por nombre
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        // Ordenar por fecha
        $order = $request->input('orden', 'ASC');
        $query->orderBy('created_at', $order);

        // Paginación
        $perPage = $request->input('per_page', 20);
        $albumes = $query->with('usuario:id_usuario,email')->paginate($perPage);

        return response()->json([
            'albums' => $albumes->items(),
            'meta' => [
                'current_page' => $albumes->currentPage(),
                'last_page' => $albumes->lastPage(),
                'per_page' => $albumes->perPage(),
                'total' => $albumes->total(),
            ],
        ]);
    }
}
