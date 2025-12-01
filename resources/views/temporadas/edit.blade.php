@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Temporada</h1>

    <form action="{{ route('temporadas.update', $temporada) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nombre --}}
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" 
                   name="nombre"
                   class="form-control"
                   value="{{ old('nombre', $temporada->nombre) }}"
                   required>
        </div>

        {{-- Fecha Inicio --}}
        <div class="mb-3">
            <label class="form-label">Fecha de Inicio</label>
            <input type="date"
                   name="fecha_inicio"
                   class="form-control"
                   value="{{ old('fecha_inicio', $temporada->fecha_inicio->format('Y-m-d')) }}"
                   required>
        </div>

        {{-- Fecha Fin: solo mostrar si existe --}}
        @if($temporada->fecha_fin)
            <div class="mb-3">
                <label class="form-label">Fecha de Fin</label>
                <input type="date"
                    name="fecha_fin"
                    class="form-control"
                    value="{{ old('fecha_fin', $temporada->fecha_fin?->format('Y-m-d')) }}">
            </div>
        @endif

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="{{ route('temporadas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
