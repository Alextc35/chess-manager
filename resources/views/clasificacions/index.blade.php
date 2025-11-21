@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Clasificaciones</h1>

    {{-- Selección de temporada --}}
    <form action="{{ route('clasificacions.rankingPost') }}" method="POST" class="row g-3 mb-4">
        @csrf

        <div class="col-md-6">
            <label class="form-label fw-bold">Seleccionar Temporada</label>
            <select name="temporada_id" class="form-select" onchange="this.form.submit()">
                <option value="" disabled>Seleccione temporada…</option>

                @foreach($temporadas as $temp)
                    <option value="{{ $temp->id }}"
                        {{ $temporada && $temporada->id == $temp->id ? 'selected' : '' }}>
                        {{ $temp->nombre }} 
                        ({{ \Carbon\Carbon::parse($temp->fecha_inicio)->format('d/m/Y') }})
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if(!$temporada)
        <div class="alert alert-info">No hay temporadas creadas todavía.</div>
        @return
    @endif

    {{-- ========================== --}}
    {{--      LIGA LOCAL           --}}
    {{-- ========================== --}}
    <h3 class="mt-4">Clasificación — Liga Local</h3>

    @if(empty($rankingLocal) || count($rankingLocal) === 0)
        <div class="alert alert-secondary">No hay alumnos de Liga Local en esta temporada.</div>
    @else
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Alumno</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankingLocal as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item['alumno']->nombre }} {{ $item['alumno']->apellidos }}</td>
                    <td><strong>{{ $item['puntos'] }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ========================== --}}
    {{--     LIGA INFANTIL         --}}
    {{-- ========================== --}}
    <h3 class="mt-5">Clasificación — Liga Infantil</h3>

    @if(empty($rankingInfantil) || count($rankingInfantil) === 0)
        <div class="alert alert-secondary">No hay alumnos de Liga Infantil en esta temporada.</div>
    @else
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Alumno</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankingInfantil as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item['alumno']->nombre }} {{ $item['alumno']->apellidos }}</td>
                    <td><strong>{{ $item['puntos'] }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection