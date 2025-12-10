<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\EnfrentamientoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemporadaAlumnoController;
use App\Http\Controllers\AuthController;

// Dashboard protegido
Route::get('/', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Rutas protegidas
Route::middleware('auth')->group(function () {

    Route::resource('alumnos', AlumnoController::class);
    Route::resource('temporadas', TemporadaController::class);
    Route::resource('clasificacions', ClasificacionController::class);
    Route::resource('enfrentamientos', EnfrentamientoController::class);

    Route::get('temporadas/{temporada}/alumnos', [TemporadaAlumnoController::class, 'edit'])
        ->name('temporadas.alumnos.edit');
    Route::post('temporadas/{temporada}/alumnos', [TemporadaAlumnoController::class, 'update'])
        ->name('temporadas.alumnos.update');

    Route::get('temporadas/{temporada}/enfrentamientos', [TemporadaController::class, 'enfrentamientos'])
        ->name('temporadas.enfrentamientos');
    Route::delete('temporadas/{temporada}/enfrentamientos/{enfrentamiento}', [EnfrentamientoController::class, 'destroy'])
        ->name('enfrentamientos.destroy');

    Route::get('temporadas/{temporada}/clasificacion', [TemporadaController::class, 'clasificacion'])
        ->name('temporadas.clasificacion');

    Route::patch('/temporadas/{temporada}/finalizar', [TemporadaController::class, 'finalizar'])
        ->name('temporadas.finalizar');

    Route::post('/enfrentamientos/generar-sesiones', [EnfrentamientoController::class, 'generarSesiones'])
        ->name('enfrentamientos.generarSesiones');
    Route::post('/enfrentamientos/guardar-sesion', [EnfrentamientoController::class, 'guardarSesion'])
        ->name('enfrentamientos.guardarSesion');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Login y autenticación
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post')
    ->middleware('guest');