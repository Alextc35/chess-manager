<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Alumno::query();

        // Agrupar la búsqueda por nombre o apellidos
        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                ->orWhere('apellidos', 'like', "%{$search}%");
            });
        }

        // Filtro por liga
        if ($request->has('liga') && in_array($request->liga, ['local', 'infantil'])) {
            $query->where('liga', $request->liga);
        }

        $alumnos = $query->orderBy('nombre')->paginate(5)->withQueryString();

        if ($request->ajax()) {
            return view('alumnos.partials.tabla', compact('alumnos'))->render();
        }

        return view('alumnos.index', compact('alumnos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('alumnos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'liga' => 'required|in:local,infantil',
        ]);

        Alumno::create($request->all());

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Alumno $alumno)
    {
        return view('alumnos.show', compact('alumno'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alumno $alumno)
    {
        return view('alumnos.edit', compact('alumno'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'liga' => 'required|in:local,infantil',
        ]);

        $alumno->update($request->all());

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alumno $alumno)
    {
        $alumno->delete();

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno eliminado correctamente.');
    }
}
