@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Alumnos</h1>

    <input type="text" id="busqueda" class="form-control mb-3" placeholder="Buscar alumno por nombre o apellido">

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
    const tabla = document.getElementById('tabla-alumnos');
    let timeout = null;

    // Función para cargar tabla con AJAX
    function cargarTabla(url) {
        const query = input.value;
        fetch(`${url}&q=${encodeURIComponent(query)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => tabla.innerHTML = html);
    }

    // Búsqueda en vivo
    input.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => cargarTabla('{{ route("alumnos.index") }}?page=1'), 300);
    });

    // Capturar clicks en links de paginación
    tabla.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' && e.target.closest('.pagination')) {
            e.preventDefault();
            cargarTabla(e.target.href);
        }
    });
});
</script>
@endsection
