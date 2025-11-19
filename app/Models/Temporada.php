<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'temporada_alumno');
    }

    public function clasificacions()
    {
        return $this->hasMany(Clasificacion::class);
    }

    public function enfrentamientos()
    {
        return $this->hasMany(Enfrentamiento::class);
    }

    public function calcularClasificacion()
    {
        // Inicializamos array de puntos
        $clasificacion = [];

        // Recorrer enfrentamientos de esa temporada
        foreach ($this->enfrentamientos as $enf) {

            // Asegurarnos de que los alumnos existan en el array
            if (!isset($clasificacion[$enf->alumno1_id])) {
                $clasificacion[$enf->alumno1_id] = 0;
            }

            if (!isset($clasificacion[$enf->alumno2_id])) {
                $clasificacion[$enf->alumno2_id] = 0;
            }

            // Repartir puntos
            switch ($enf->resultado) {

                case 'blancas': // alumno1 gana
                    $clasificacion[$enf->alumno1_id] += 3;
                    $clasificacion[$enf->alumno2_id] += 1;
                    break;

                case 'negras': // alumno2 gana
                    $clasificacion[$enf->alumno1_id] += 1;
                    $clasificacion[$enf->alumno2_id] += 3;
                    break;

                case 'tablas':
                    $clasificacion[$enf->alumno1_id] += 2;
                    $clasificacion[$enf->alumno2_id] += 2;
                    break;
            }
        }

        // Ordenar descendentemente por puntos
        arsort($clasificacion);

        return $clasificacion; // devuelve [alumno_id => puntos]
    }

}
