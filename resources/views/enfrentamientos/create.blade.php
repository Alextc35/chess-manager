@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nuevo Enfrentamiento</h1>

    <form action="{{ route('enfrentamientos.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Alumno 1</label>
            <select name="alumno1_id" class="form-control" required>
                @foreach($alumnos as $alumno)
                    <option value="{{ $alumno->id }}">{{ $alumno->nombre }} {{ $alumno->apellidos }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Alumno 2</label>
            <select name="alumno2_id" class="form-control" required>
                @foreach($alumnos as $alumno)
                    <option value="{{ $alumno->id }}">{{ $alumno->nombre }} {{ $alumno->apellidos }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Resultado</label>
            <select name="resultado" class="form-control">
                <option value="">-- Sin definir --</option>
                <option value="blancas">Blancas</option>
                <option value="negras">Negras</option>
                <option value="tablas">Tablas</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
