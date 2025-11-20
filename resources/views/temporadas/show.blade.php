@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Temporada: {{ $temporada->nombre }}</h1>

    <ul class="list-group mb-3">
        <li class="list-group-item">
            <strong>Nombre:</strong> {{ $temporada->nombre }}
        </li>

        <li class="list-group-item">
            <strong>Fecha Inicio:</strong>
            {{ \Carbon\Carbon::parse($temporada->fecha_inicio)->format('d/m/Y') }}
        </li>

        <li class="list-group-item">
            <strong>Fecha Fin:</strong>
            @if($temporada->fecha_fin)
                {{ \Carbon\Carbon::parse($temporada->fecha_fin)->format('d/m/Y') }}
            @else
                <span>—</span>
            @endif
        </li>

        <li class="list-group-item">
            <strong>Estado:</strong>
            @if($temporada->fecha_fin)
                <span class="badge bg-secondary">Finalizada</span>
            @else
                <span class="badge bg-success">EN CURSO</span>
            @endif
        </li>
    </ul>

    {{-- Botón Finalizar Temporada solo si no está finalizada --}}
    @if(!$temporada->fecha_fin)
        <form action="{{ route('temporadas.finalizar', $temporada) }}" 
              method="POST"
              class="d-inline">
            @csrf
            @method('PATCH')

            <button class="btn btn-success"
                    onclick="return confirm('¿Finalizar la temporada?')">
                Finalizar Temporada
            </button>
        </form>
    @endif

    <a href="{{ route('temporadas.edit', $temporada) }}" class="btn btn-warning">Editar</a>
    <a href="{{ route('temporadas.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection