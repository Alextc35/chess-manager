<?php

namespace App\Http\Controllers;

use App\Models\Temporada;
use App\Models\Alumno;
use Illuminate\Http\Request;

class TemporadaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $temporadas = Temporada::orderBy('fecha_inicio', 'desc')->paginate(3);
        
        return view('temporadas.index', compact('temporadas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('temporadas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
        ]);

        Temporada::create([
            'nombre' => $request->nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => null, // siempre null al crear
        ]);

        return redirect()->route('temporadas.index')
                         ->with('success', 'Temporada creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Temporada $temporada)
    {
        return view('temporadas.show', compact('temporada'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Temporada $temporada)
    {
        return view('temporadas.edit', compact('temporada'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Temporada $temporada)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
        ]);

        $temporada->update($request->all());

        return redirect()->route('temporadas.index')
                         ->with('success', 'Temporada actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Temporada $temporada)
    {
        $temporada->delete();

        return redirect()->route('temporadas.index')
                         ->with('success', 'Temporada eliminada correctamente.');
    }

    public function enfrentamientos(Temporada $temporada)
    {
        $enfrentamientos = $temporada->enfrentamientos()
            ->with(['alumno1', 'alumno2'])
            ->orderBy('fecha', 'asc')
            ->get();

        return view('temporadas.enfrentamientos', compact('temporada', 'enfrentamientos'));
    }

    public function clasificacion(Temporada $temporada)
    {
        $clasificacion = $temporada->calcularClasificacion();

        // Traer los alumnos ordenados según la clasificación
        $alumnos = Alumno::whereIn('id', array_keys($clasificacion))
            ->get()
            ->keyBy('id');

        return view('temporadas.clasificacion', compact('temporada', 'clasificacion', 'alumnos'));
    }

    public function finalizar(Temporada $temporada)
    {
        $temporada->update([
            'fecha_fin' => now(),
        ]);

        return redirect()->route('temporadas.index')
            ->with('success', 'La temporada ha sido finalizada.');
    }
}
