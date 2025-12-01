@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Resultado - {{ $enfrentamiento->temporada->nombre }} ({{ $enfrentamiento->liga }})</h1>

    <form action="{{ route('enfrentamientos.update', $enfrentamiento) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Alumno 1 (Blancas)</label>
            <input type="text" class="form-control" value="{{ $enfrentamiento->alumno1->nombre }} {{ $enfrentamiento->alumno1->apellidos }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Alumno 2 (Negras)</label>
            <input type="text" class="form-control" value="{{ $enfrentamiento->alumno2->nombre }} {{ $enfrentamiento->alumno2->apellidos }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Resultado</label>
            <select name="resultado" class="form-select" required>
                <option value="">Seleccionar...</option>
                <option value="blancas" {{ $enfrentamiento->resultado === 'blancas' ? 'selected' : '' }}>Blancas gana</option>
                <option value="negras" {{ $enfrentamiento->resultado === 'negras' ? 'selected' : '' }}>Negras gana</option>
                <option value="tablas" {{ $enfrentamiento->resultado === 'tablas' ? 'selected' : '' }}>Tablas</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('enfrentamientos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
