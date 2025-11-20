<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\EnfrentamientoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemporadaAlumnoController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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
    
Route::get('ranking', [ClasificacionController::class, 'ranking'])->name('clasificacions.ranking');
Route::post('ranking', [ClasificacionController::class, 'rankingPost'])->name('clasificacions.ranking.post');
