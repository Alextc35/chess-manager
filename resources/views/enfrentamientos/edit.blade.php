@extends('layouts.app')

@section('content')
<div class="container">

    <h1>Editar Enfrentamiento - {{ $temporada->nombre }}</h1>

    <form action="{{ route('enfrentamientos.update', [$temporada, $enfrentamiento]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Alumno 1</label>
            <select name="alumno1_id" class="form-select" required>
                <option value="">Seleccionar...</option>
                @foreach ($alumnos as $alumno)
                    <option value="{{ $alumno->id }}"
                        {{ $enfrentamiento->alumno1_id == $alumno->id ? 'selected' : '' }}>
                        {{ $alumno->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Alumno 2</label>
            <select name="alumno2_id" class="form-select" required>
                <option value="">Seleccionar...</option>
                @foreach ($alumnos as $alumno)
                    <option value="{{ $alumno->id }}"
                        {{ $enfrentamiento->alumno2_id == $alumno->id ? 'selected' : '' }}>
                        {{ $alumno->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Puntos Alumno 1</label>
            <input type="number"
                   name="puntos1"
                   class="form-control"
                   value="{{ $enfrentamiento->puntos1 }}"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Puntos Alumno 2</label>
            <input type="number"
                   name="puntos2"
                   class="form-control"
                   value="{{ $enfrentamiento->puntos2 }}"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date"
                   name="fecha"
                   class="form-control"
                   value="{{ $enfrentamiento->fecha }}">
        </div>

        <button class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('temporadas.enfrentamientos', $temporada) }}" class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>
@endsection
