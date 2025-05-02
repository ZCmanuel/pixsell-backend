<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Usuario extends Model implements JWTSubject
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = ['nombre', 'email', 'contraseña', 'rol'];

    /**
     * Relación uno a muchos con la tabla Album
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Album, Usuario>
     */
    public function albumes()
    {
        return $this->hasMany(Album::class, 'id_usuario');
    }


    /**
     *  Metoodo de JWT para obtener el identificador del usuario 
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Método de JWT para obtener las reclamaciones personalizadas
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}