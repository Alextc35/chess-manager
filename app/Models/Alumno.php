<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'nombre',
        'apellidos',
        'fecha_nacimiento',
        'liga',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function clasificacions()
    {
        return $this->hasMany(Clasificacion::class);
    }

    public function enfrentamientosComoAlumno1()
    {
        return $this->hasMany(Enfrentamiento::class, 'alumno1_id');
    }

    public function enfrentamientosComoAlumno2()
    {
        return $this->hasMany(Enfrentamiento::class, 'alumno2_id');
    }

    public function temporadas()
    {
        return $this->belongsToMany(Temporada::class, 'temporada_alumno');
    }
}
