@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Temporada</h1>

    <form action="{{ route('temporadas.update', $temporada) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $temporada->nombre) }}">
        </div>
        <div class="mb-3">
            <label>Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', $temporada->fecha_inicio) }}">
        </div>
        <div class="mb-3">
            <label>Fecha de Fin</label>
            <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin', $temporada->fecha_fin) }}">
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('temporadas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
