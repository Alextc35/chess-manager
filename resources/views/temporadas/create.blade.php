@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nueva Temporada</h1>

    <form action="{{ route('temporadas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha Fin</label>
            <input type="date" name="fecha_fin" class="form-control" required>
        </div>

        <button class="btn btn-primary">Guardar</button>
        <a href="{{ route('temporadas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
