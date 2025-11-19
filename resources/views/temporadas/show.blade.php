@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Temporada: {{ $temporada->nombre }}</h1>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Nombre:</strong> {{ $temporada->nombre }}</li>
        <li class="list-group-item"><strong>Fecha Inicio:</strong> {{ $temporada->fecha_inicio }}</li>
        <li class="list-group-item"><strong>Fecha Fin:</strong> {{ $temporada->fecha_fin }}</li>
    </ul>

    <a href="{{ route('temporadas.edit', $temporada) }}" class="btn btn-warning">Editar</a>

    <a href="{{ route('temporadas.alumnos.edit', $temporada) }}" class="btn btn-info">
        Gestionar alumnos participantes
    </a>

    <a href="{{ route('temporadas.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
