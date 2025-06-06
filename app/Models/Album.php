<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $table = 'albumes';
    protected $primaryKey = 'id_album';

    protected $fillable = ['id_usuario', 'nombre', 'descripcion', 'fecha_creacion', 'fecha_act', 'estado'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function multimedia()
    {
        return $this->hasMany(Multimedia::class, 'id_album');
    }

    public function selecciones()
    {
        return $this->hasMany(Seleccion::class, 'id_album');
    }
}
