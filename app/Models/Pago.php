<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'alumno_id',
        'mes',
        'estado',
        'fecha_pago',
        'importe',
        'observaciones',
    ];

    protected $casts = [
        'mes' => 'date',
        'fecha_pago' => 'date',
        'importe' => 'decimal:2',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}
