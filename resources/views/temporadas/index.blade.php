@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Temporadas</h1>

    <a href="{{ route('temporadas.create') }}" class="btn btn-primary mb-3">Nueva Temporada</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($temporadas as $temporada)
            <tr>
                <td>{{ $temporada->nombre }}</td>
                <td>{{ $temporada->fecha_inicio->format('d/m/Y') }}</td>

                {{-- Fecha fin o EN CURSO --}}
                <td>
                    @if($temporada->fecha_fin)
                        {{ $temporada->fecha_fin->format('d/m/Y') }}
                    @else
                        —
                    @endif
                </td>

                {{-- Estado --}}
                <td>
                    @if($temporada->fecha_fin)
                        <span class="badge bg-secondary">Finalizada</span>
                    @else
                        <span class="badge bg-success">EN CURSO</span>
                    @endif
                </td>

                <td>
                    {{-- <a href="{{ route('temporadas.show', $temporada) }}" class="btn btn-info btn-sm">Ver</a> --}}

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

                    {{-- Botón FINALIZAR TEMPORADA si aún no tiene fecha_fin --}}
                    @if(!$temporada->fecha_fin)
                        <form action="{{ route('temporadas.finalizar', $temporada) }}" 
                              method="POST" 
                              style="display:inline-block;">
                            @csrf
                            @method('PATCH')

                            <button type="submit" 
                                    class="btn btn-success btn-sm"
                                    onclick="return confirm('¿Finalizar esta temporada?')">
                                Finalizar
                            </button>
                        </form>
                    @endif

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        {{ $temporadas->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
