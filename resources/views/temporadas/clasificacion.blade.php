@extends('layouts.app')

@section('content')
<div class="container">

    <h1>Clasificación - {{ $temporada->nombre }}</h1>

    <a href="{{ route('temporadas.show', $temporada) }}" class="btn btn-secondary mb-3">Volver</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Puesto</th>
                <th>Alumno</th>
                <th>Puntos</th>
            </tr>
        </thead>
        <tbody>
            @php $puesto = 1; @endphp
            @foreach($clasificacion as $alumno_id => $puntos)
                <tr>
                    <td>{{ $puesto++ }}</td>
                    <td>{{ $alumnos[$alumno_id]->nombre }}</td>
                    <td>{{ $puntos }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
