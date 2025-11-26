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
        ->paginate(10);

        return view('enfrentamientos.index', compact('enfrentamientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $alumnos = Alumno::all();
        $temporadas = Temporada::whereNull('fecha_fin')
            ->orWhere('fecha_fin', '>=', now())
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        $ligas = ['local', 'infantil'];

        return view('enfrentamientos.multiple', compact('alumnos', 'temporadas', 'ligas'));
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
        $temporada = $enfrentamiento->temporada;
        $enfrentamiento->delete();

        return redirect()
            ->route('temporadas.enfrentamientos', $temporada)
            ->with('success', 'Enfrentamiento eliminado correctamente.');
    }

    /**
     * Genera enfrentamientos tipo mini-round-robin considerando asistencia y alternancia de colores.
     */
    public function generarSesiones(Request $request)
    {
        $request->validate([
            'alumnos' => 'required|array|min:2',
            'liga' => 'required|in:local,infantil',
            'temporada_id' => 'required|exists:temporadas,id',
        ]);

        $temporada = Temporada::find($request->temporada_id);
        $liga = $request->liga;

        // Alumnos presentes de la liga seleccionada
        $alumnosIds = Alumno::whereIn('id', $request->alumnos)
                            ->where('liga', $liga)
                            ->pluck('id')
                            ->toArray();

        // Asociar automáticamente a la temporada si no lo están
        $temporada->alumnos()->syncWithoutDetaching($alumnosIds);

        shuffle($alumnosIds); // aleatorizar para variar colores

        $combinaciones = [];
        $bye = [];

        while (count($alumnosIds) >= 2) {
            $al1 = array_shift($alumnosIds);
            $paired = false;

            foreach ($alumnosIds as $index => $al2) {
                // Comprobar enfrentamientos previos
                $matches = Enfrentamiento::where('temporada_id', $temporada->id)
                    ->where(function($q) use ($al1, $al2) {
                        $q->where(function($q2) use ($al1, $al2) {
                            $q2->where('alumno1_id', $al1)->where('alumno2_id', $al2);
                        })->orWhere(function($q2) use ($al1, $al2) {
                            $q2->where('alumno1_id', $al2)->where('alumno2_id', $al1);
                        });
                    })->get();

                // Si ya jugaron ambos colores, saltamos
                if ($matches->count() >= 2) continue;

                // Determinar colores: si ya jugaron, invertimos
                $alumno1_id = $al1;
                $alumno2_id = $al2;
                if ($matches->count() === 1) {
                    $prev = $matches->first();
                    if ($prev->alumno1_id == $al1) {
                        $alumno1_id = $al2;
                        $alumno2_id = $al1;
                    }
                }

                $combinaciones[] = [
                    'alumno1_id' => $alumno1_id,
                    'alumno2_id' => $alumno2_id,
                    'liga' => $liga,
                ];

                array_splice($alumnosIds, $index, 1);
                $paired = true;
                break;
            }

            if (!$paired) {
                $bye[] = $al1;
            }
        }

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

    /**
     * Guarda los resultados de la sesión generada.
     */
    public function guardarSesion(Request $request)
    {
        $request->validate([
            'temporada_id' => 'required|exists:temporadas,id',
            'resultados' => 'required|array|min:1',
        ]);

        $temporada = Temporada::find($request->temporada_id);

        foreach ($request->resultados as $res) {
            $al1 = $res['alumno1_id'];
            $al2 = $res['alumno2_id'] ?? null;
            $resultado = $res['resultado'] ?? null;

            if ($al2) {
                // Revisar si ya jugaron dos veces (ida y vuelta)
                $matches = Enfrentamiento::where('temporada_id', $temporada->id)
                    ->where(function($q) use ($al1, $al2) {
                        $q->where(function($q2) use ($al1, $al2) {
                            $q2->where('alumno1_id', $al1)->where('alumno2_id', $al2);
                        })->orWhere(function($q2) use ($al1, $al2) {
                            $q2->where('alumno1_id', $al2)->where('alumno2_id', $al1);
                        });
                    })->count();

                if ($matches < 2) {
                    // Crear un nuevo enfrentamiento, nunca sobrescribir
                    Enfrentamiento::create([
                        'temporada_id' => $temporada->id,
                        'alumno1_id'   => $al1,
                        'alumno2_id'   => $al2,
                        'resultado'    => $resultado,
                        'fecha'        => now(),
                    ]);
                }

                $temporada->alumnos()->syncWithoutDetaching([$al1, $al2]);
            } else {
                $temporada->alumnos()->syncWithoutDetaching([$al1]);
            }
        }

        return redirect()->route('enfrentamientos.index')
                        ->with('success', 'Sesión registrada correctamente.');
    }
}
