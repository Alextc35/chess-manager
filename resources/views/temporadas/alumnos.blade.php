@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Alumnos que participan en: {{ $temporada->nombre }}</h1>

    <form action="{{ route('temporadas.alumnos.update', $temporada) }}" method="POST">
        @csrf

        <div class="card mb-3">
            <div class="card-header">
                Selecciona los alumnos participantes
            </div>

            <div class="card-body">
                @foreach($alumnos as $alumno)
                    <div class="form-check">
                        <input 
                            class="form-check-input"
                            type="checkbox"
                            name="alumnos[]"
                            value="{{ $alumno->id }}"
                            id="alumno{{ $alumno->id }}"
                            {{ in_array($alumno->id, $alumnosSeleccionados) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="alumno{{ $alumno->id }}">
                            {{ $alumno->nombre }} {{ $alumno->apellidos }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('temporadas.show', $temporada) }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
