@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nuevo Enfrentamiento Múltiple</h1>

    <form method="POST" action="{{ route('enfrentamientos.generar') }}">
        @csrf

        <h4>Selecciona los alumnos presentes:</h4>

        <div class="row">
            @foreach($alumnos as $alumno)
                <div class="col-4">
                    <label>
                        <input type="checkbox" name="alumnos[]" value="{{ $alumno->id }}">
                        {{ $alumno->nombre }} {{ $alumno->apellidos }}
                    </label>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary mt-3">
            Generar Enfrentamientos
        </button>
    </form>
</div>
@endsection
