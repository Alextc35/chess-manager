@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Alumnos</h1>

    <div class="mb-3 d-flex align-items-center">
        <div class="input-group me-2">
            <input type="text" id="busqueda" class="form-control" placeholder="Buscar alumno por nombre o apellido" value="{{ request('q') }}">
            <button class="btn btn-outline-secondary d-none" type="button" id="btn-reset" title="Borrar filtros">&times;</button>
        </div>

        <select id="filtro-liga" class="form-select me-2">
            <option value="">Todas las ligas</option>
            <option value="local" {{ request('liga') == 'local' ? 'selected' : '' }}>Local</option>
            <option value="infantil" {{ request('liga') == 'infantil' ? 'selected' : '' }}>Infantil</option>
        </select>
    </div>

    <a href="{{ route('alumnos.create') }}" class="btn btn-success mb-3">Nuevo Alumno</a>

    <div id="tabla-alumnos">
        @include('alumnos.partials.tabla', ['alumnos' => $alumnos])
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('busqueda');
    const select = document.getElementById('filtro-liga');
    const tabla = document.getElementById('tabla-alumnos');
    const btnReset = document.getElementById('btn-reset');
    let timeout = null;

    // Función para actualizar tabla
    function cargarTabla(url) {
        const query = input.value;
        const liga = select.value;

        fetch(`${url}&q=${encodeURIComponent(query)}&liga=${encodeURIComponent(liga)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => tabla.innerHTML = html);

        if (query !== '') {
            btnReset.classList.remove('d-none');
        } else {
            btnReset.classList.add('d-none');
        }
    }

    // Búsqueda en vivo con debounce
    input.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => cargarTabla('{{ route("alumnos.index") }}?page=1'), 300);
    });

    // Filtro de liga en vivo
    select.addEventListener('change', function() {
        cargarTabla('{{ route("alumnos.index") }}?page=1');
    });

    // Paginación AJAX
    tabla.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' && e.target.closest('.pagination')) {
            e.preventDefault();
            cargarTabla(e.target.href);
        }
    });

    // Botón “Borrar filtros”
    btnReset.addEventListener('click', function() {
        input.value = '';
        cargarTabla('{{ route("alumnos.index") }}?page=1');
    });

    if(input.value !== '') {
        btnReset.classList.remove('d-none');
    }
});
</script>
@endsection