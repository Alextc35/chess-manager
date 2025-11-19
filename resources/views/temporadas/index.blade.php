@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Temporadas</h1>

    <a href="{{ route('temporadas.create') }}" class="btn btn-primary mb-3">Nueva Temporada</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($temporadas as $temporada)
            <tr>
                <td>{{ $temporada->nombre }}</td>
                <td>{{ $temporada->fecha_inicio }}</td>
                <td>{{ $temporada->fecha_fin }}</td>
                <td>
                    <a href="{{ route('temporadas.show', $temporada) }}" class="btn btn-info btn-sm">Ver</a>

                    <a href="{{ route('temporadas.alumnos.edit', $temporada) }}" class="btn btn-success btn-sm">
                        Alumnos
                    </a>

                    <a href="{{ route('temporadas.edit', $temporada) }}" class="btn btn-warning btn-sm">Editar</a>

                    <form action="{{ route('temporadas.destroy', $temporada) }}" 
                          method="POST" 
                          style="display:inline-block;">
                        @csrf
                        @method('DELETE')

                        <button type="submit" 
                                class="btn btn-danger btn-sm" 
                                onclick="return confirm('¿Seguro que quieres eliminarla?')">
                            Eliminar
                        </button>
                    </form>

                </td>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
