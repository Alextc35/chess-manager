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

    <form action="{{ route('enfrentamientos.storeMultiple') }}" method="POST">
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
                @foreach($combinaciones as $i => $comb)
                    @php
                        $a1 = $alumnos->firstWhere('id', $comb['alumno1_id']);
                        $a2 = $alumnos->firstWhere('id', $comb['alumno2_id']);
                    @endphp
                    <tr>
                        <td>{{ $a1->nombre }} {{ $a1->apellidos }}</td>
                        <td>{{ $a2->nombre }} {{ $a2->apellidos }}</td>
                        <td>
                            <select name="resultados[{{ $i }}][resultado]" class="form-control">
                                <option value="">—</option>
                                <option value="blancas">Ganan blancas</option>
                                <option value="negras">Ganan negras</option>
                                <option value="tablas">Tablas</option>
                            </select>

                            <input type="hidden" name="resultados[{{ $i }}][alumno1_id]" value="{{ $comb['alumno1_id'] }}">
                            <input type="hidden" name="resultados[{{ $i }}][alumno2_id]" value="{{ $comb['alumno2_id'] }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-success">Finalizar Sesión</button>
    </form>
</div>
@endsection
