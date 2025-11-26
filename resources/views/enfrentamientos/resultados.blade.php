@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Resultados de la Sesión</h1>

    {{-- Mostrar BYE si hay --}}
    @if(isset($bye) && count($bye) > 0)
        @foreach($bye as $id)
            @php $alumnoBye = $alumnos->firstWhere('id', $id); @endphp
            <div class="alert alert-info">
                <strong>{{ $alumnoBye->nombre }} {{ $alumnoBye->apellidos }}</strong> descansa esta ronda.
            </div>
        @endforeach
    @endif

    @if(isset($combinaciones) && count($combinaciones) > 0)
        <form action="{{ route('enfrentamientos.guardarSesion') }}" method="POST">
            @csrf
            <input type="hidden" name="temporada_id" value="{{ $temporada->id }}">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Blancas</th>
                        <th>Negras</th>
                        <th>Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($combinaciones as $i => $s)
                        <tr>
                            <td>
                                {{ $alumnos->find($s['alumno1_id'])->nombre }} {{ $alumnos->find($s['alumno1_id'])->apellidos }}
                            </td>
                            <td>
                                @if($s['alumno2_id'])
                                    {{ $alumnos->find($s['alumno2_id'])->nombre }} {{ $alumnos->find($s['alumno2_id'])->apellidos }}
                                @else
                                    <em>Descansa</em>
                                @endif
                            </td>
                            <td>
                                @if($s['alumno2_id'])
                                    <select name="resultados[{{ $i }}][resultado]" class="form-control">
                                        <option value="">—</option>
                                        <option value="blancas">Ganan blancas</option>
                                        <option value="negras">Ganan negras</option>
                                        <option value="tablas">Tablas</option>
                                    </select>

                                    <input type="hidden" name="resultados[{{ $i }}][alumno1_id]" value="{{ $s['alumno1_id'] }}">
                                    <input type="hidden" name="resultados[{{ $i }}][alumno2_id]" value="{{ $s['alumno2_id'] }}">
                                @else
                                    <input type="hidden" name="resultados[{{ $i }}][alumno1_id]" value="{{ $s['alumno1_id'] }}">
                                    <input type="hidden" name="resultados[{{ $i }}][alumno2_id]" value="">
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button class="btn btn-success">Finalizar Sesión</button>
        </form>
    @else
        <div class="alert alert-info">
            Todos los alumnos descansan esta ronda. No hay enfrentamientos para registrar.
        </div>
        <a href="{{ route('enfrentamientos.index') }}" class="btn btn-primary">Volver a Enfrentamientos</a>
    @endif

</div>
@endsection
