<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Usuario extends Authenticatable implements JWTSubject
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'email',
        'contraseña',
        'rol'
    ];

    /**
     * Relación uno a muchos con la tabla Album
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Album, Usuario>
     */
    public function albumes()
    {
        return $this->hasMany(Album::class, 'id_usuario');
    }


    /*
     * Devuelve el identificador único del usuario (para el token)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Cancion, Usuario>
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Devuelve cualquier información adicional que quieras incluir en el token (opcional)
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return []; // Puedes añadir "rol" u otros datos aquí
    }

    public function getAuthPassword()
    {
        return $this->contraseña;
    }

}
