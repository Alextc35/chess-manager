<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enfrentamiento extends Model
{
    protected $fillable = [
        'temporada_id',
        'alumno1_id',
        'alumno2_id',
        'resultado',
        'fecha',
    ];

    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    public function alumno1()
    {
        return $this->belongsTo(Alumno::class, 'alumno1_id');
    }

    public function alumno2()
    {
        return $this->belongsTo(Alumno::class, 'alumno2_id');
    }
}
