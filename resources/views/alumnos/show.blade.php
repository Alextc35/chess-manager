@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Alumno: {{ $alumno->nombre }} {{ $alumno->apellidos }}</h1>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Nombre:</strong> {{ $alumno->nombre }}</li>
        <li class="list-group-item"><strong>Apellidos:</strong> {{ $alumno->apellidos }}</li>
        <li class="list-group-item"><strong>Fecha de Nacimiento:</strong> {{ $alumno->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</li>
        <li class="list-group-item"><strong>Fecha de Alta:</strong> {{ $alumno->fecha_alta?->format('d/m/Y') ?? '—' }}</li>
        <li class="list-group-item"><strong>Liga:</strong> {{ $alumno->liga }}</li>
        <li class="list-group-item">
            <strong>Estado de cuotas:</strong>
            @if($alumno->tienePagosPendientesHasta())
                <span class="badge bg-warning text-dark">{{ $alumno->totalPagosPendientesHasta() }} pendiente{{ $alumno->totalPagosPendientesHasta() > 1 ? 's' : '' }}</span>
            @else
                <span class="badge bg-success">Al día</span>
            @endif
        </li>
    </ul>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Historial de pagos</span>
            <a href="{{ route('pagos.index') }}" class="btn btn-sm btn-outline-primary">Gestionar pagos</a>
        </div>
        <div class="card-body">
            @if($pagos->isEmpty())
                <p class="mb-0 text-muted">Todavía no hay pagos registrados para este alumno.</p>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($pagos as $item)
                        @php
                            $pago = $item['pago'];
                            $mesHistorial = $item['mes'];
                            $estadoHistorial = $item['estado'];
                            $tienePagoRegistrado = $item['editable'];
                        @endphp
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                <div>
                                    <div class="fw-semibold">{{ $mesHistorial->locale('es')->translatedFormat('F Y') }}</div>
                                    <div class="mt-2">
                                        @if($estadoHistorial === 'pagado')
                                            <span class="badge bg-success">Pagado</span>
                                        @elseif($estadoHistorial === 'ausencia')
                                            <span class="badge bg-dark-subtle text-dark">Ausencia</span>
                                        @elseif($estadoHistorial === 'exento')
                                            <span class="badge bg-secondary">Exento</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary pago-toggle" data-target="pago-editor-{{ $mesHistorial->format('Y-m') }}">
                                        Editar
                                    </button>
                                    @if($tienePagoRegistrado)
                                        <form action="{{ route('pagos.destroy', $pago) }}" method="POST" onsubmit="return confirm('¿Eliminar este pago?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Borrar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div class="row mt-3">
                                @if($estadoHistorial === 'pagado')
                                    <div class="col-md-4">
                                        <div class="small text-muted">Fecha de pago</div>
                                        <div>{{ $pago?->fecha_pago?->format('d/m/Y') ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted">Importe</div>
                                        <div>{{ $pago?->importe !== null ? number_format((float) $pago->importe, 2, ',', '.') . ' €' : '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted">Observaciones</div>
                                        <div>{{ $pago?->observaciones ?: '—' }}</div>
                                    </div>
                                @elseif($estadoHistorial === 'exento')
                                    <div class="col-md-12">
                                        <div class="small text-muted">Observaciones</div>
                                        <div>{{ $pago?->observaciones ?: '—' }}</div>
                                    </div>
                                @endif
                            </div>

                            <div id="pago-editor-{{ $mesHistorial->format('Y-m') }}" class="border-top mt-3 pt-3 d-none">
                                <form action="{{ $tienePagoRegistrado ? route('pagos.update', $pago) : route('pagos.store') }}" method="POST" class="row g-2 align-items-end pago-edit-form-clean">
                                    @csrf
                                    @if($tienePagoRegistrado)
                                        @method('PATCH')
                                    @else
                                        <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                                        <input type="hidden" name="mes" value="{{ $mesHistorial->format('Y-m') }}">
                                    @endif
                                    <div class="col-md-3">
                                        <label class="form-label">Estado</label>
                                        <select name="estado" class="form-select form-select-sm pago-edit-estado-clean">
                                            <option value="pendiente" {{ $estadoHistorial === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="pagado" {{ $estadoHistorial === 'pagado' ? 'selected' : '' }}>Pagado</option>
                                            <option value="exento" {{ $estadoHistorial === 'exento' ? 'selected' : '' }}>Exento</option>
                                            <option value="ausencia" {{ $estadoHistorial === 'ausencia' ? 'selected' : '' }}>Ausencia</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 pago-edit-campos-fecha-clean{{ $estadoHistorial !== 'pagado' ? ' d-none' : '' }}">
                                        <label class="form-label">Fecha de pago</label>
                                        <input type="date" name="fecha_pago" class="form-control form-control-sm pago-edit-fecha-clean" value="{{ optional($pago?->fecha_pago)->format('Y-m-d') }}" {{ $estadoHistorial !== 'pagado' ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-md-2 pago-edit-campos-importe-clean{{ $estadoHistorial !== 'pagado' ? ' d-none' : '' }}">
                                        <label class="form-label">Importe</label>
                                        <input type="number" step="0.01" min="0" name="importe" class="form-control form-control-sm pago-edit-importe-clean" value="{{ $pago?->importe }}" {{ $estadoHistorial !== 'pagado' ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-md-4 pago-edit-campos-observaciones-clean{{ !in_array($estadoHistorial, ['pagado', 'exento']) ? ' d-none' : '' }}">
                                        <label class="form-label">Observaciones</label>
                                        <input type="text" name="observaciones" class="form-control form-control-sm pago-edit-observaciones-clean" value="{{ $pago?->observaciones }}" {{ !in_array($estadoHistorial, ['pagado', 'exento']) ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Guardar cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    {{ $pagos->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-warning">Editar</a>
    <form action="{{ route('alumnos.destroy', $alumno) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este alumno?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Eliminar</button>
    </form>
    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pago-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            const editor = document.getElementById(button.dataset.target);

            if (!editor) {
                return;
            }

            editor.classList.toggle('d-none');
            button.textContent = editor.classList.contains('d-none') ? 'Editar' : 'Cerrar';
        });
    });

    document.querySelectorAll('.pago-edit-form-clean').forEach((form) => {
        const estado = form.querySelector('.pago-edit-estado-clean');
        const fecha = form.querySelector('.pago-edit-fecha-clean');
        const importe = form.querySelector('.pago-edit-importe-clean');
        const observaciones = form.querySelector('.pago-edit-observaciones-clean');
        const fechaWrap = form.querySelector('.pago-edit-campos-fecha-clean');
        const importeWrap = form.querySelector('.pago-edit-campos-importe-clean');
        const observacionesWrap = form.querySelector('.pago-edit-campos-observaciones-clean');

        const sync = () => {
            const esPagado = estado.value === 'pagado';
            const permiteObservaciones = ['pagado', 'exento'].includes(estado.value);

            fechaWrap?.classList.toggle('d-none', !esPagado);
            importeWrap?.classList.toggle('d-none', !esPagado);
            observacionesWrap?.classList.toggle('d-none', !permiteObservaciones);

            fecha.disabled = !esPagado;
            importe.disabled = !esPagado;
            observaciones.disabled = !permiteObservaciones;

            if (!esPagado) {
                fecha.value = '';
                importe.value = '';
            }

            if (!permiteObservaciones) {
                observaciones.value = '';
            }
        };

        estado.addEventListener('change', sync);
        sync();
    });
});
</script>
@endsection
