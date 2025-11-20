@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nueva Temporada</h1>

    <form action="{{ route('temporadas.store') }}" method="POST">
        @csrf

        {{-- Nombre --}}
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text"
                   name="nombre"
                   class="form-control"
                   value="{{ old('nombre') }}"
                   required>
        </div>

        {{-- Fecha Inicio --}}
        <div class="mb-3">
            <label class="form-label">Fecha de Inicio</label>
            <input type="date"
                   name="fecha_inicio"
                   class="form-control"
                   value="{{ old('fecha_inicio') }}"
                   required>
        </div>

        {{-- Fecha fin eliminada, se añadirá al finalizar temporada --}}
        
        <button type="submit" class="btn btn-primary">Crear Temporada</button>
        <a href="{{ route('temporadas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection