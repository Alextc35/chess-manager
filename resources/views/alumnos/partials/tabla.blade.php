<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Cuotas</th>
                <th>Fecha Nacimiento</th>
                <th>Fecha Alta</th>
                <th>Liga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumnos as $alumno)
            <tr>
                <td>
                    <a href="{{ route('alumnos.show', $alumno) }}">{{ $alumno->nombre }}</a>
                    @if($alumno->tienePagosPendientesHasta())
                        <span class="ms-1 text-warning" title="Tiene cuotas pendientes">&#9888;</span>
                    @endif
                </td>
                <td>{{ $alumno->apellidos }}</td>
                <td>
                    @if($alumno->tienePagosPendientesHasta())
                        <span class="badge bg-warning text-dark">{{ $alumno->totalPagosPendientesHasta() }} pendiente{{ $alumno->totalPagosPendientesHasta() > 1 ? 's' : '' }}</span>
                    @else
                        <span class="badge bg-success">Al día</span>
                    @endif
                </td>
                <td>{{ $alumno->fecha_nacimiento ? $alumno->fecha_nacimiento->format('d/m/Y') : '—' }}</td>
                <td>{{ $alumno->fecha_alta ? $alumno->fecha_alta->format('d/m/Y') : '—' }}</td>
                <td>{{ $alumno->liga }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No se encontraron alumnos.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $alumnos->links('pagination::bootstrap-5') }}
</div>
