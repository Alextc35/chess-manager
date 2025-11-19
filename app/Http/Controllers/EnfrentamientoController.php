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
        $alumnos = Alumno::orderBy('nombre')->get();
        $temporada = Temporada::orderBy('fecha_inicio', 'desc')->first(); // última temporada

        return view('enfrentamientos.create', compact('alumnos', 'temporada'));
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
}
