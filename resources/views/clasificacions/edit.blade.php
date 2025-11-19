@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Clasificación</h1>

    <form action="{{ route('clasificacions.update', $clasificacion) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Alumno</label>
            <select name="alumno_id" class="form-control">
                @foreach($alumnos as $alumno)
                    <option value="{{ $alumno->id }}" {{ $alumno->id == $clasificacion->alumno_id ? 'selected' : '' }}>
                        {{ $alumno->nombre }} {{ $alumno->apellidos }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Temporada</label>
            <select name="temporada_id" class="form-control">
                @foreach($temporadas as $temporada)
                    <option value="{{ $temporada->id }}" {{ $temporada->id == $clasificacion->temporada_id ? 'selected' : '' }}>
                        {{ $temporada->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Puntos</label>
            <input type="number" name="puntos" class="form-control" value="{{ old('puntos', $clasificacion->puntos) }}">
        </div>

        <div class="mb-3">
            <label>Posición</label>
            <input type="number" name="posicion" class="form-control" value="{{ old('posicion', $clasificacion->posicion) }}">
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('clasificacions.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
