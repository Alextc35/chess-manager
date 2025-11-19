@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Alumno</h1>

    <form action="{{ route('alumnos.update', $alumno) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $alumno->nombre) }}">
        </div>

        <div class="mb-3">
            <label>Apellidos</label>
            <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $alumno->apellidos) }}">
        </div>

        <div class="mb-3">
            <label>Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento) }}">
        </div>

        <div class="mb-3">
            <label>Liga</label>
            <select name="liga" class="form-control">
                <option value="local" {{ $alumno->liga == 'local' ? 'selected' : '' }}>Local</option>
                <option value="infantil" {{ $alumno->liga == 'infantil' ? 'selected' : '' }}>Infantil</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
