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
    public function index(Request $request)
    {
        $temporadas = Temporada::orderBy('fecha_inicio', 'desc')->get();
        
        $temporada_id = $request->input('temporada_id');
        $liga = $request->input('liga', 'local');

        // Elegir temporada
        $temporada = $temporadas->firstWhere('id', $temporada_id) ?? $temporadas->first();

        $ranking = collect();

        if ($temporada) {
            $rankingArray = $this->calcularRankingLiga($temporada, $liga);

            $page = $request->input('page', 1);
            $perPage = 16;

            $ranking = new \Illuminate\Pagination\LengthAwarePaginator(
                array_slice($rankingArray, ($page - 1) * $perPage, $perPage),
                count($rankingArray),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(), // mantiene temporada_id y liga
                ]
            );
        }

        return view('clasificacions.index', compact('temporadas', 'temporada', 'liga', 'ranking'));
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

    private function calcularRankingLiga($temporada, $liga)
    {
        $alumnos = $temporada->alumnos->where('liga', $liga);
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
