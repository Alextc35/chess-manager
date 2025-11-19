@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Clasificación de {{ $clasificacion->alumno->nombre }} {{ $clasificacion->alumno->apellidos }}</h1>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Alumno:</strong> {{ $clasificacion->alumno->nombre }} {{ $clasificacion->alumno->apellidos }}</li>
        <li class="list-group-item"><strong>Temporada:</strong> {{ $clasificacion->temporada->nombre }}</li>
        <li class="list-group-item"><strong>Puntos:</strong> {{ $clasificacion->puntos }}</li>
        <li class="list-group-item"><strong>Posición:</strong> {{ $clasificacion->posicion ?? '-' }}</li>
    </ul>

    <a href="{{ route('clasificacions.edit', $clasificacion) }}" class="btn btn-warning">Editar</a>
    <a href="{{ route('clasificacions.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
