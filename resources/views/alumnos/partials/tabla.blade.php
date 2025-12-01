<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Fecha Nacimiento</th>
            <th>Liga</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($alumnos as $alumno)
        <tr>
            <td>{{ $alumno->nombre }}</td>
            <td>{{ $alumno->apellidos }}</td>
            <td>{{ $alumno->fecha_nacimiento ? $alumno->fecha_nacimiento->format('d/m/Y') : '—' }}</td>
            <td>{{ $alumno->liga }}</td>
            <td>
                <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-warning btn-sm">Editar</a>
                <form action="{{ route('alumnos.destroy', $alumno) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminarlo?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No se encontraron alumnos.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $alumnos->links('pagination::bootstrap-5') }}
</div>
