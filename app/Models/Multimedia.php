<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Multimedia extends Model
{
    protected $table = 'multimedia';
    protected $primaryKey = 'id_multimedia';

    protected $fillable = ['id_album', 'ruta_archivo', 'tipo'];

    public function album()
    {
        return $this->belongsTo(Album::class, 'id_album');
    }

    public function selecciones()
    {
        return $this->hasMany(Seleccion::class, 'id_multimedia');
    }
}
