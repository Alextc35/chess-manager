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
            'temporada_id' => 'required|exists:temporadas,id',
        ]);

        $temporada = Temporada::find($request->temporada_id);
        $liga = $request->liga;

        // Solo alumnos de la liga seleccionada
        $alumnosIds = Alumno::whereIn('id', $request->alumnos)
                            ->where('liga', $liga)
                            ->pluck('id')
                            ->toArray();

        shuffle($alumnosIds);

        $combinaciones = [];
        $bye = [];

        while (count($alumnosIds) >= 2) {
            $al1 = array_shift($alumnosIds);
            $paired = false;

            // Intentamos emparejar al1 con el primer posible compañero
            foreach ($alumnosIds as $index => $al2) {

                // Comprobar si ya jugaron ambos colores
                $alreadyPlayed = Enfrentamiento::where('temporada_id', $temporada->id)
                    ->where(function($q) use ($al1, $al2) {
                        $q->where(function($q2) use ($al1, $al2) {
                            $q2->where('alumno1_id', $al1)
                            ->where('alumno2_id', $al2);
                        })->orWhere(function($q2) use ($al1, $al2) {
                            $q2->where('alumno1_id', $al2)
                            ->where('alumno2_id', $al1);
                        });
                    })->count();

                if ($alreadyPlayed < 2) {
                    // Pueden jugar (menos de 2 enfrentamientos)
                    $combinaciones[] = [
                        'alumno1_id' => $al1,
                        'alumno2_id' => $al2,
                        'liga' => $liga
                    ];

                    // Eliminamos al2 de la lista de pendientes
                    array_splice($alumnosIds, $index, 1);
                    $paired = true;
                    break;
                }
            }

            if (!$paired) {
                // No hay compañero disponible => queda libre
                $bye[] = $al1;
            }
        }

        // Si queda un alumno suelto al final
        if (count($alumnosIds) === 1) {
            $bye[] = array_shift($alumnosIds);
        }

        $alumnos = Alumno::whereIn('id', $request->alumnos)->get();

        return view('enfrentamientos.resultados', [
            'combinaciones' => $combinaciones,
            'bye' => $bye,
            'alumnos' => $alumnos,
            'temporada' => $temporada,
        ]);
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'temporada_id' => 'required|exists:temporadas,id',
            'resultados' => 'required|array|min:1',
        ]);

        $temporada = Temporada::find($request->temporada_id);
        $resultados = $request->resultados;

        foreach ($resultados as $res) {
            $al1 = $res['alumno1_id'];
            $al2 = $res['alumno2_id'];
            $resultado = $res['resultado'] ?? null;

            // Revisar si ya existe un enfrentamiento con estos colores
            $exists = Enfrentamiento::where('temporada_id', $temporada->id)
                ->where('alumno1_id', $al1)
                ->where('alumno2_id', $al2)
                ->exists();

            if (!$exists) {
                // Crear nuevo enfrentamiento
                Enfrentamiento::create([
                    'temporada_id' => $temporada->id,
                    'alumno1_id'   => $al1,
                    'alumno2_id'   => $al2,
                    'resultado'    => $resultado,
                    'fecha'        => now(),
                ]);

                // Asociar alumnos a la temporada
                $temporada->alumnos()->syncWithoutDetaching([$al1, $al2]);
            }
        }

        return redirect()->route('enfrentamientos.index')
                        ->with('success', 'Sesión creada con todos los enfrentamientos.');
    }
}
