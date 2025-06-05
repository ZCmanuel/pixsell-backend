<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController
{

    /**
     * Obtiene estadísticas de álbumes.
     * ENDPOINT: /api/admin/estadisticas/albums -> GET
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function albumEstads()
    {
        // Total de álbumes
        $totalAlbumes = \App\Models\Album::count();

        // Número de álbumes por estado
        $albumesPorEstado = \App\Models\Album::select('estado', \DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        // Número de álbumes finalizados
        $albumesFinalizados = \App\Models\Album::where('estado', 'finalizado')->count();

        // Álbumes creados por semana
        $albumesPorSemana = \App\Models\Album::select(\DB::raw('YEAR(created_at) as year, WEEK(created_at) as week, count(*) as total'))
            ->groupBy('year', 'week')
            ->orderBy('year', 'DESC')
            ->orderBy('week', 'DESC')
            ->get();

        return response()->json([
            'total_albumes' => $totalAlbumes,
            'albumes_por_estado' => $albumesPorEstado,
            'albumes_finalizados' => $albumesFinalizados,
            'albumes_por_semana' => $albumesPorSemana,
        ]);
    }

    /**
     * Obtiene estadísticas de usuarios.
     * ENDPOINT: /api/admin/estadisticas/users -> GET
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersEstads()
    {
        // Total de usuarios registrados
        $totalUsuarios = \App\Models\Usuario::count();

        // Usuarios registrados por semana en el último mes
        $usuariosPorSemana = \App\Models\Usuario::select(\DB::raw('YEAR(created_at) as year, WEEK(created_at) as week, count(*) as total'))
            ->where('created_at', '>=', now()->subMonth()) // Filtrar usuarios registrados en el último mes
            ->groupBy('year', 'week')
            ->orderBy('year', 'DESC')
            ->orderBy('week', 'DESC')
            ->get();

        return response()->json([
            'total_usuarios' => $totalUsuarios,
            'usuarios_por_semana' => $usuariosPorSemana,
        ]);
    }


    public function userAlbumStats(Request $request)
    {
        // Obtener el usuario autenticado
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Número total de álbumes del usuario
        $totalAlbumes = \App\Models\Album::where('id_usuario', $usuario->id_usuario)->count();

        // Número de álbumes en estado "pendiente"
        $albumesPendientes = \App\Models\Album::where('id_usuario', $usuario->id_usuario)
            ->where('estado', 'pendiente')
            ->count();

        return response()->json([
            'total_albumes' => $totalAlbumes,
            'albumes_pendientes' => $albumesPendientes,
        ]);
    }
}
