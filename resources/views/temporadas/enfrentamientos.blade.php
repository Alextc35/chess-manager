@extends('layouts.app')

@section('content')
<div class="container">

    <h1>Enfrentamientos - {{ $temporada->nombre }}</h1>

    <a href="{{ route('enfrentamientos.create', $temporada) }}" class="btn btn-primary mb-3">
        Nuevo Enfrentamiento
    </a>

    <a href="{{ route('temporadas.show', $temporada) }}" class="btn btn-secondary mb-3">
        Volver a la temporada
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($enfrentamientos->isEmpty())
        <div class="alert alert-info">
            No hay enfrentamientos registrados todavía.
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Alumno 1</th>
                    <th>Alumno 2</th>
                    <th>Puntos 1</th>
                    <th>Puntos 2</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enfrentamientos as $enfrentamiento)
                    <tr>
                        <td>{{ $enfrentamiento->alumno1->nombre }}</td>
                        <td>{{ $enfrentamiento->alumno2->nombre }}</td>
                        <td>{{ $enfrentamiento->puntos1 }}</td>
                        <td>{{ $enfrentamiento->puntos2 }}</td>
                        <td>{{ $enfrentamiento->fecha ?? '—' }}</td>
                        <td>
                            <a href="{{ route('enfrentamientos.edit', [$temporada, $enfrentamiento]) }}" class="btn btn-warning btn-sm">
                                Editar
                            </a>

                            <form action="{{ route('enfrentamientos.destroy', [$temporada, $enfrentamiento]) }}"
                                  method="POST"
                                  style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                
                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Eliminar enfrentamiento?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection
