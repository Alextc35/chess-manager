@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Alumno: {{ $alumno->nombre }} {{ $alumno->apellidos }}</h1>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Nombre:</strong> {{ $alumno->nombre }}</li>
        <li class="list-group-item"><strong>Apellidos:</strong> {{ $alumno->apellidos }}</li>
        <li class="list-group-item"><strong>Fecha de Nacimiento:</strong> {{ $alumno->fecha_nacimiento }}</li>
        <li class="list-group-item"><strong>Liga:</strong> {{ $alumno->liga }}</li>
    </ul>

    <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-warning">Editar</a>
    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
