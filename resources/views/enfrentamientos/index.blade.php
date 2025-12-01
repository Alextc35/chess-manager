@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Enfrentamientos</h1>

    <a href="{{ route('enfrentamientos.create') }}" class="btn btn-primary mb-3">Nuevo Enfrentamiento</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Temporada</th>
                <th>Jugador 1 (Blancas)</th>
                <th>Jugador 2 (Negras)</th>
                <th>Resultado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enfrentamientos as $e)
            <tr>
                <td>{{ $e->temporada->nombre }}</td>
                <td>{{ $e->alumno1->nombre }} {{ $e->alumno1->apellidos }}</td>
                <td>{{ $e->alumno2->nombre }} {{ $e->alumno2->apellidos }}</td>
                <td>{{ $e->resultado ?? '-' }}</td>
                <td>{{ $e->fecha ?? '-' }}</td>
                <td>
                    {{-- <a href="{{ route('enfrentamientos.show', $e) }}" class="btn btn-info btn-sm">Ver</a> --}}
                    <a href="{{ route('enfrentamientos.edit', $e) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('enfrentamientos.destroy', $e) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminarlo?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        {{ $enfrentamientos->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
