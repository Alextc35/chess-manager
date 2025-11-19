<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clasificacion extends Model
{
    protected $fillable = [
        'alumno_id',
        'temporada_id',
        'puntos',
        'posicion',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }
}
