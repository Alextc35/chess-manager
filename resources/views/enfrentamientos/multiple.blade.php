@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Generar Sesión de Enfrentamientos</h1>

    <form action="{{ route('enfrentamientos.generarSesiones') }}" method="POST">
        @csrf

        {{-- Selección de Liga --}}
        <div class="mb-3">
            <label class="form-label">Seleccionar Liga</label>
            <select name="liga" id="ligaSelect" class="form-select">
                @foreach($ligas as $l)
                    <option value="{{ $l }}" {{ $loop->first ? 'selected' : '' }}>{{ ucfirst($l) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Seleccionar Temporada</label>
            <select name="temporada_id" class="form-select" required>
                @foreach($temporadas as $temporada)
                    <option value="{{ $temporada->id }}">{{ $temporada->nombre }} ({{ \Carbon\Carbon::parse($temporada->fecha_inicio)->format('d/m/Y') }})</option>
                @endforeach
            </select>
        </div>

        {{-- Contenedor para checkboxes de alumnos --}}
        <div id="alumnosContainer" class="mb-3">
            {{-- Se llenará vía JS --}}
        </div>

        <button class="btn btn-success">Generar Sesión</button>
    </form>
</div>

<script>
    const alumnosContainer = document.getElementById('alumnosContainer');
    const ligaSelect = document.getElementById('ligaSelect');

    // Datos de alumnos agrupados por liga
    const alumnosData = @json($alumnos->groupBy('liga'));

    function renderAlumnos(liga) {
        alumnosContainer.innerHTML = '';
        const alumnos = alumnosData[liga] || [];
        alumnos.forEach(a => {
            const div = document.createElement('div');
            div.className = 'form-check';
            div.innerHTML = `
                <input class="form-check-input" type="checkbox" name="alumnos[]" value="${a.id}" id="alumno${a.id}">
                <label class="form-check-label" for="alumno${a.id}">${a.nombre} ${a.apellidos}</label>
            `;
            alumnosContainer.appendChild(div);
        });
    }

    ligaSelect.addEventListener('change', e => renderAlumnos(e.target.value));

    // Renderizar la liga por defecto al cargar la página
    renderAlumnos(ligaSelect.value);
</script>
@endsection
