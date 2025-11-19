@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard</h1>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Alumnos</h5>
                    <p class="card-text fs-3">{{ $totalAlumnos }}</p>
                    <a href="{{ route('alumnos.index') }}" class="btn btn-light btn-sm">Ver alumnos</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Temporadas</h5>
                    <p class="card-text fs-3">{{ $totalTemporadas }}</p>
                    <a href="{{ route('temporadas.index') }}" class="btn btn-light btn-sm">Ver temporadas</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Clasificaciones</h5>
                    <p class="card-text fs-3">{{ $totalClasificaciones }}</p>
                    <a href="{{ route('clasificacions.index') }}" class="btn btn-light btn-sm">Ver clasificaciones</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Enfrentamientos</h5>
                    <p class="card-text fs-3">{{ $totalEnfrentamientos }}</p>
                    <a href="{{ route('enfrentamientos.index') }}" class="btn btn-light btn-sm">Ver enfrentamientos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
