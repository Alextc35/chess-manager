<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Temporada;
use App\Models\Clasificacion;
use App\Models\Enfrentamiento;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAlumnos = Alumno::count();
        $totalTemporadas = Temporada::count();
        $totalClasificaciones = Clasificacion::count();
        $totalEnfrentamientos = Enfrentamiento::count();

        return view('dashboard', compact(
            'totalAlumnos',
            'totalTemporadas',
            'totalClasificaciones',
            'totalEnfrentamientos'
        ));
    }
}
