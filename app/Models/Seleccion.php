<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seleccion extends Model
{
    protected $table = 'selecciones';
    protected $primaryKey = 'id_seleccion';

    protected $fillable = ['id_album', 'id_multimedia'];

    public function album()
    {
        return $this->belongsTo(Album::class, 'id_album');
    }

    public function multimedia()
    {
        return $this->belongsTo(Multimedia::class, 'id_multimedia');
    }
}
