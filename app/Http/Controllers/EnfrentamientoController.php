<?php

namespace App\Http\Controllers;

use App\Models\Enfrentamiento;
use App\Models\Alumno;
use App\Models\Temporada;
use Illuminate\Http\Request;

class EnfrentamientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enfrentamientos = Enfrentamiento::with(['alumno1', 'alumno2', 'temporada'])
        ->orderBy('fecha', 'desc')
        ->get();
        return view('enfrentamientos.index', compact('enfrentamientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $temporada = Temporada::orderBy('fecha_inicio', 'desc')->first();
        $ligas = ['local', 'infantil'];

        return view('enfrentamientos.multiple', compact('temporada', 'ligas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'alumno1_id' => 'required|exists:alumnos,id|different:alumno2_id',
            'alumno2_id' => 'required|exists:alumnos,id',
            'resultado' => 'nullable|in:blancas,negras,tablas',
            'temporada_id' => 'nullable|exists:temporadas,id'
        ]);

        // Si no se pasa temporada, usamos la última
        $temporada = $request->temporada_id 
            ? Temporada::find($request->temporada_id)
            : Temporada::orderBy('fecha_inicio', 'desc')->first();

        // Creamos el enfrentamiento
        $enfrentamiento = Enfrentamiento::create([
            'alumno1_id' => $request->alumno1_id,
            'alumno2_id' => $request->alumno2_id,
            'resultado' => $request->resultado,
            'temporada_id' => $temporada->id,
            'fecha' => now()
        ]);

        // Asociamos automáticamente alumnos a la temporada si no lo están
        $temporada->alumnos()->syncWithoutDetaching([$request->alumno1_id, $request->alumno2_id]);

        return redirect()->route('enfrentamientos.index')
            ->with('success', 'Enfrentamiento creado y alumnos asociados a la temporada.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enfrentamiento $enfrentamiento)
    {
        return view('enfrentamientos.show', compact('enfrentamiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Temporada $temporada, Enfrentamiento $enfrentamiento)
    {
        // Filtrar solo los alumnos que pertenecen a esta temporada
        $alumnos = $temporada->alumnos;

        return view('enfrentamientos.edit', compact('temporada', 'enfrentamiento', 'alumnos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Temporada $temporada, Enfrentamiento $enfrentamiento)
    {
        $request->validate([
            'alumno1_id' => 'required|different:alumno2_id',
            'alumno2_id' => 'required|different:alumno1_id',
            'puntos1' => 'required|integer|min:0',
            'puntos2' => 'required|integer|min:0',
            'fecha' => 'nullable|date'
        ]);

        $enfrentamiento->update($request->all());

        return redirect()
            ->route('temporadas.enfrentamientos', $temporada)
            ->with('success', 'Enfrentamiento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Temporada $temporada, Enfrentamiento $enfrentamiento)
    {
        $enfrentamiento->delete();

        return redirect()
            ->route('temporadas.enfrentamientos', $temporada)
            ->with('success', 'Enfrentamiento eliminado correctamente.');
    }

    public function generar(Request $request)
    {
        $request->validate([
            'alumnos' => 'required|array|min:2',
            'liga' => 'required|in:local,infantil',
        ]);

        $ids = $request->alumnos;
        shuffle($ids);

        $combinaciones = [];
        $count = count($ids);
        $bye = null;

        if ($count % 2 !== 0) {
            $bye = array_pop($ids);
            $count--;
        }

        for ($i = 0; $i < $count; $i += 2) {
            $combinaciones[] = [
                'alumno1_id' => $ids[$i],
                'alumno2_id' => $ids[$i + 1],
                'liga' => $request->liga
            ];
        }

        $alumnos = Alumno::whereIn('id', $request->alumnos)->get();
        $temporada = Temporada::orderBy('fecha_inicio', 'desc')->first();

        return view('enfrentamientos.resultados', compact('combinaciones', 'bye', 'alumnos', 'temporada'));
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'resultados' => 'required|array',
            'resultados.*.alumno1_id' => 'required|exists:alumnos,id',
            'resultados.*.alumno2_id' => 'required|exists:alumnos,id',
            'resultados.*.resultado' => 'nullable|in:blancas,negras,tablas',
        ]);

        $resultados = $request->input('resultados'); // <-- aquí lo defines
        $temporada = Temporada::orderBy('fecha_inicio', 'desc')->first();

        foreach ($resultados as $res) {
            $exists = Enfrentamiento::where('temporada_id', $temporada->id)
                ->where(function($q) use ($res) {
                    $q->where(function($q2) use ($res) {
                        $q2->where('alumno1_id', $res['alumno1_id'])
                        ->where('alumno2_id', $res['alumno2_id']);
                    })->orWhere(function($q2) use ($res) {
                        $q2->where('alumno1_id', $res['alumno2_id'])
                        ->where('alumno2_id', $res['alumno1_id']);
                    });
                })->exists();

            if (!$exists) {
                Enfrentamiento::create([
                    'temporada_id' => $temporada->id,
                    'alumno1_id'   => $res['alumno1_id'],
                    'alumno2_id'   => $res['alumno2_id'],
                    'resultado'    => $res['resultado'] ?? null,
                    'fecha'        => now(),
                ]);

                $temporada->alumnos()->syncWithoutDetaching([
                    $res['alumno1_id'], 
                    $res['alumno2_id']
                ]);
            }
        }

        return redirect()->route('enfrentamientos.index')
                        ->with('success', 'Sesión creada con todos los enfrentamientos.');
    }
}
