@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Clasificaciones</h1>

    <form action="{{ route('clasificacions.ranking.post') }}" method="POST" class="mb-4">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label>Selecciona Temporada</label>
                <select name="temporada_id" class="form-control" required>
                    <option value="">-- Selecciona --</option>
                    @foreach($temporadas as $temp)
                        <option value="{{ $temp->id }}" 
                            {{ isset($temporada) && $temp->id == $temporada->id ? 'selected' : '' }}>
                            {{ $temp->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Ver Ranking</button>
            </div>
        </div>
    </form>

    @isset($ranking)
        <h3>Ranking: {{ $temporada->nombre }}</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Posición</th>
                    <th>Alumno</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ranking as $index => $r)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $r['alumno']->nombre }} {{ $r['alumno']->apellidos }}</td>
                        <td>{{ $r['puntos'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endisset
</div>
@endsection
