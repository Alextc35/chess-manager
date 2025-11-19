@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Clasificación</h1>

    <form action="{{ route('clasificacions.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Alumno</label>
            <select name="alumno_id" class="form-control">
                @foreach($alumnos as $alumno)
                    <option value="{{ $alumno->id }}">{{ $alumno->nombre }} {{ $alumno->apellidos }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Temporada</label>
            <select name="temporada_id" class="form-control">
                @foreach($temporadas as $temporada)
                    <option value="{{ $temporada->id }}">{{ $temporada->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Puntos</label>
            <input type="number" name="puntos" class="form-control" value="{{ old('puntos', 0) }}">
        </div>

        <div class="mb-3">
            <label>Posición</label>
            <input type="number" name="posicion" class="form-control" value="{{ old('posicion') }}">
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('clasificacions.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
