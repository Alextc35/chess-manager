<?php

namespace App\Http\Controllers;

use App\Models\Clasificacion;
use App\Models\Alumno;
use App\Models\Temporada;
use App\Models\Enfrentamiento;
use Illuminate\Http\Request;

class ClasificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $temporadas = Temporada::orderBy('fecha_inicio')->get();
        $temporada = $temporadas->last();

        $ranking = null;
        if ($temporada) {
            $ranking = $this->calcularRanking($temporada);
        }

        return view('clasificacions.index', compact('temporadas', 'temporada', 'ranking'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $alumnos = Alumno::orderBy('apellidos')->get();
        $temporadas = Temporada::orderBy('fecha_inicio')->get();
        return view('clasificacions.create', compact('alumnos', 'temporadas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'temporada_id' => 'required|exists:temporadas,id',
            'puntos' => 'required|integer|min:0',
            'posicion' => 'nullable|integer|min:1',
        ]);

        Clasificacion::create($request->all());

        return redirect()->route('clasificacions.index')
                         ->with('success', 'Clasificación creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Clasificacion $clasificacion)
    {
        return view('clasificacions.show', compact('clasificacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clasificacion $clasificacion)
    {
        $alumnos = Alumno::orderBy('apellidos')->get();
        $temporadas = Temporada::orderBy('fecha_inicio')->get();
        return view('clasificacions.edit', compact('clasificacion', 'alumnos', 'temporadas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Clasificacion $clasificacion)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'temporada_id' => 'required|exists:temporadas,id',
            'puntos' => 'required|integer|min:0',
            'posicion' => 'nullable|integer|min:1',
        ]);

        $clasificacion->update($request->all());

        return redirect()->route('clasificacions.index')
                         ->with('success', 'Clasificación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clasificacion $clasificacion)
    {
        $clasificacion->delete();

        return redirect()->route('clasificacions.index')
                         ->with('success', 'Clasificación eliminada correctamente.');
    }

    public function rankingPost(Request $request)
    {
        $request->validate([
            'temporada_id' => 'required|exists:temporadas,id',
        ]);

        $temporadas = Temporada::orderBy('fecha_inicio')->get();
        $temporada = Temporada::find($request->temporada_id);

        $ranking = $this->calcularRanking($temporada);

        return view('clasificacions.index', compact('temporadas', 'temporada', 'ranking'));
    }

    private function calcularRanking($temporada)
    {
        $alumnos = $temporada->alumnos;
        $ranking = [];

        foreach ($alumnos as $alumno) {
            $puntos = 0;
            $enfrentamientos = Enfrentamiento::where('temporada_id', $temporada->id)
                ->where(function($q) use ($alumno) {
                    $q->where('alumno1_id', $alumno->id)
                    ->orWhere('alumno2_id', $alumno->id);
                })->get();

            foreach ($enfrentamientos as $enf) {
                if ($enf->resultado === null) continue;

                if (($enf->alumno1_id == $alumno->id && $enf->resultado === 'blancas') ||
                    ($enf->alumno2_id == $alumno->id && $enf->resultado === 'negras')) {
                    $puntos += 3;
                } elseif ($enf->resultado === 'tablas') {
                    $puntos += 2;
                } else {
                    $puntos += 1;
                }
            }

            $ranking[] = [
                'alumno' => $alumno,
                'puntos' => $puntos
            ];
        }

        usort($ranking, fn($a, $b) => $b['puntos'] <=> $a['puntos']);

        return $ranking;
    }
}
