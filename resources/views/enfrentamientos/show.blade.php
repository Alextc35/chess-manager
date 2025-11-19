@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Enfrentamiento</h1>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Temporada:</strong> {{ $enfrentamiento->temporada->nombre }}</li>
        <li class="list-group-item"><strong>Jugador 1 (Blancas):</strong> {{ $enfrentamiento->alumno1->nombre }} {{ $enfrentamiento->alumno1->apellidos }}</li>
        <li class="list-group-item"><strong>Jugador 2 (Negras):</strong> {{ $enfrentamiento->alumno2->nombre }} {{ $enfrentamiento->alumno2->apellidos }}</li>
        <li class="list-group-item"><strong>Resultado:</strong> {{ $enfrentamiento->resultado ?? '-' }}</li>
        <li class="list-group-item"><strong>Fecha:</strong> {{ $enfrentamiento->fecha ?? '-' }}</li>
    </ul>

    <a href="{{ route('enfrentamientos.edit', $enfrentamiento) }}" class="btn btn-warning">Editar</a>
    <a href="{{ route('enfrentamientos.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
