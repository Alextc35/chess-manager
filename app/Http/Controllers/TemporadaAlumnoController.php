<?php

namespace App\Http\Controllers;

use App\Models\Temporada;
use App\Models\Alumno;
use Illuminate\Http\Request;

class TemporadaAlumnoController extends Controller
{
    public function edit(Temporada $temporada)
    {
        $alumnos = Alumno::orderBy('apellidos')->get();
        $alumnosSeleccionados = $temporada->alumnos->pluck('id')->toArray();

        return view('temporadas.alumnos', compact('temporada', 'alumnos', 'alumnosSeleccionados'));
    }

    public function update(Request $request, Temporada $temporada)
    {
        $temporada->alumnos()->sync($request->alumnos ?? []);

        return redirect()->route('temporadas.show', $temporada)
                         ->with('success', 'Alumnos actualizados correctamente para la temporada.');
    }
}
