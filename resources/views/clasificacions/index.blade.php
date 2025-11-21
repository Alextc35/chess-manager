@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Clasificaciones</h1>

    {{-- Formulario para seleccionar temporada y liga --}}
    <form action="{{ route('clasificacions.rankingPost') }}" method="POST" class="row g-3 mb-4">
        @csrf
        <div class="col-md-4">
            <label class="form-label fw-bold">Temporada</label>
            <select name="temporada_id" class="form-select" onchange="this.form.submit()">
                <option value="" disabled>Seleccione temporada…</option>
                @foreach($temporadas as $temp)
                    <option value="{{ $temp->id }}" @selected($temporada && $temporada->id == $temp->id)>
                        {{ $temp->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Liga</label>
            <select name="liga" class="form-select" onchange="this.form.submit()">
                <option value="local" {{ $liga === 'local' ? 'selected' : '' }}>Liga Local</option>
                <option value="infantil" {{ $liga === 'infantil' ? 'selected' : '' }}>Liga Infantil</option>
            </select>
        </div>
    </form>

    @if(!$temporada)
        <div class="alert alert-info">No hay temporadas creadas todavía.</div>
    @elseif(!$liga)
        <div class="alert alert-secondary">Seleccione una liga para ver la clasificación.</div>
    @else
        <h4 class="mt-4 mb-2">Clasificación — Liga {{ ucfirst($liga) }}</h4>
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Alumno</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ranking as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item['alumno']->nombre }} {{ $item['alumno']->apellidos }}</td>
                        <td><strong>{{ $item['puntos'] }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No hay alumnos en esta liga.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</div>
@endsection