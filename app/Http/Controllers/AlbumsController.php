<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Multimedia;

class AlbumsController
{
    /**
     * Lista todos los álbumes con paginación, filtros y ordenación.
     * ENDPOINT: /api/admin/albums -> GET
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

    /**
     *  Obtiene los álbumes de un usuario.
     * ENDPOINT: /api/admi/albums/{id_usuario} -> GET
     * @param int $id_usuario
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getAlbumsByUser(int $id_usuario)
    {
        // Verificar que el usuario exista
        $usuario = \App\Models\Usuario::find($id_usuario);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Obtener los álbumes del usuario ordenados de más recientes a menos recientes
        $albumes = Album::where('id_usuario', $id_usuario)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'albums' => $albumes,
        ]);
    }

    /**
     * Obtiene los álbumes del usuario autenticado.
     * ENDPOINT: /api/user/albums -> GET
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getUserAlbums(Request $request)
    {
        // Obtener el usuario autenticado
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Obtener los álbumes del usuario autenticado
        $albumes = Album::where('id_usuario', $usuario->id_usuario)
            ->orderBy('created_at', 'DESC') // Ordenar de más recientes a menos recientes
            ->get();

        return response()->json([
            'albums' => $albumes,
        ]);
    }

    /**
     * Crea un nuevo álbum con imágenes asociadas.
     * ENDPOINT: /api/admin/albums/ -> POST
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAlbum(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $this->validateAlbumData($request);

        if ($validatedData instanceof \Illuminate\Http\JsonResponse) {
            return $validatedData; // Retornar errores de validación
        }
        try {
            // Crear el álbum en la base de datos
            $album = $this->createAlbumRecord($validatedData);

            // Guardar las imágenes y asociarlas al álbum
            $multimediaEntries = $this->storeAlbumImages($request->file('fotos'), $validatedData['id_user'], $album->id_album);

            return response()->json([
                'message' => 'Álbum creado correctamente',
                'album' => $album,
                'multimedia' => $multimediaEntries,
            ], 201);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error 500
            return response()->json(['error' => 'Error al crear el álbum: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene un álbum por su ID con todos sus datos.
     * ENDPOINT: /api/albums/{id_album} -> GET
     *
     * @param int $id_album
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlbumById(Request $request, int $id_album)
    {
        $usuario = $request->user(); // Obtener el usuario autenticado

        // Verificar si el usuario es cliente
        if ($usuario->rol === 'cliente') {
            // Buscar el álbum del cliente por ID
            $album = Album::with(['multimedia', 'selecciones.multimedia'])
                ->where('id_usuario', $usuario->id_usuario)
                ->find($id_album);

            if (!$album) {
                return response()->json(['error' => 'Álbum no encontrado o no tienes permiso para acceder a él'], 403);
            }
        } else if ($usuario->rol === 'fotógrafo') {
            // Si es administrador, puede acceder a cualquier álbum
            $album = Album::with(['multimedia', 'selecciones.multimedia'])
                ->find($id_album);

            if (!$album) {
                return response()->json(['error' => 'Álbum no encontrado'], 404);
            }
        } else {
            // Si el rol no es válido, devolver un error
            return response()->json(['error' => 'No tienes permiso para acceder a este recurso'], 403);
        }
        // Formatear la respuesta
        return response()->json([
            'id_album' => $album->id_album,
            'nombre' => $album->nombre,
            'descripcion' => $album->descripcion,
            'estado' => $album->estado,
            'created_at' => $album->created_at,
            'updated_at' => $album->updated_at,
            'imagenes' => $album->multimedia->map(function ($media) {
                return [
                    'id_multimedia' => $media->id_multimedia,
                    'ruta_archivo' => $media->ruta_archivo,
                    'url' => $media->url,
                    'tipo' => $media->tipo,
                ];
            }),
            'seleccionadas' => $album->selecciones->map(function ($seleccion) {
                return [
                    'id_seleccion' => $seleccion->id_seleccion,
                    'id_multimedia' => $seleccion->id_multimedia,
                    'id_album' => $seleccion->id_album,
                    'multimedia' => [
                        'id_multimedia' => $seleccion->multimedia->id_multimedia,
                        'ruta_archivo' => $seleccion->multimedia->ruta_archivo,
                        'url' => $seleccion->multimedia->url,
                        'tipo' => $seleccion->multimedia->tipo,
                    ],
                ];
            }),
        ]);
    }

    /**
     * Valida los datos de entrada para crear un álbum.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    private function validateAlbumData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:150',
            'descripcion' => 'required|string|max:255',
            'id_user' => 'required|exists:usuarios,id_usuario', // Asegura que el usuario exista
            'fotos' => 'required|array',
            'fotos.*' => 'file|image|max:5120', // Cada archivo debe ser una imagen y no superar los 5MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return $validator->validated();
    }

    /**
     * Crea un registro de álbum en la base de datos.
     *
     * @param array $data
     * @return \App\Models\Album
     */
    private function createAlbumRecord(array $data)
    {
        return Album::create([
            'id_usuario' => $data['id_user'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'estado' => 'pendiente', // Estado inicial del álbum
        ]);
    }

    /**
     * Guarda las imágenes en el almacenamiento y crea entradas en la tabla multimedia.
     *
     * @param array $images
     * @param int $userId
     * @param int $albumId
     * @return array
     */
    private function storeAlbumImages(array $images, int $userId, int $albumId)
    {
        // Obtener el nombre del usuario
        $usuario = \App\Models\Usuario::find($userId);
        if (!$usuario) {
            throw new \Exception("Usuario no encontrado");
        }

        // Construir el basePath con el formato solicitado
        $albumFolder = "album_{$albumId}";
        $basePath = "user_{$userId}/{$albumFolder}";

        $multimediaEntries = [];

        foreach ($images as $index => $image) {
            // Generar un nombre único para la imagen
            $extension = $image->getClientOriginalExtension(); // Obtener la extensión original
            $imageName = "image_{$index}." . $extension; // Crear el nombre personalizado

            // Guardar la imagen en el almacenamiento con el nombre personalizado
            $path = $image->storeAs($basePath, $imageName, 'public'); // Especificar el disco 'public'

            // Crear una entrada en la tabla multimedia
            $multimediaEntries[] = [
                'id_album' => $albumId,
                'ruta_archivo' => $path, // Ruta interna del archivo
                'url' => Storage::url($path), // Ruta pública del archivo
                'tipo' => 'foto', // Tipo de archivo (por defecto 'foto')
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar las entradas en la tabla multimedia
        Multimedia::insert($multimediaEntries);

        return $multimediaEntries;
    }
}
