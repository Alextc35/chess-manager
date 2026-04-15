@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Pagos Mensuales</h1>

    <form method="GET" action="{{ route('pagos.index') }}" class="row g-3 mb-4" id="pagos-filtros-form">
        <div class="col-md-3">
            <label class="form-label">Mes</label>
            <input type="month" name="mes" class="form-control" value="{{ $mesSeleccionado->format('Y-m') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Liga</label>
            <select name="liga" class="form-select">
                <option value="">Todas</option>
                <option value="local" {{ request('liga') === 'local' ? 'selected' : '' }}>Local</option>
                <option value="infantil" {{ request('liga') === 'infantil' ? 'selected' : '' }}>Infantil</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="pagado" {{ request('estado') === 'pagado' ? 'selected' : '' }}>Pagado</option>
                <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="exento" {{ request('estado') === 'exento' ? 'selected' : '' }}>Exento</option>
                <option value="ausencia" {{ request('estado') === 'ausencia' ? 'selected' : '' }}>Ausencia</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Alumno</label>
            <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Buscar">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <a href="{{ route('pagos.index') }}" class="btn btn-outline-secondary">Mes actual</a>
        </div>
    </form>

    @php $estadoSeleccionado = request('estado'); @endphp
    <div class="row mb-4">
        @if(!$estadoSeleccionado || $estadoSeleccionado === 'pagado')
            <div class="{{ $estadoSeleccionado ? 'col-md-12' : 'col-md-4' }}">
                <div class="alert alert-success mb-0">Pagados: <strong>{{ $resumen['pagado'] }}</strong></div>
            </div>
        @endif
        @if(!$estadoSeleccionado || $estadoSeleccionado === 'pendiente')
            <div class="{{ $estadoSeleccionado ? 'col-md-12' : 'col-md-4' }}">
                <div class="alert alert-warning mb-0">Pendientes: <strong>{{ $resumen['pendiente'] }}</strong></div>
            </div>
        @endif
        @if(!$estadoSeleccionado || $estadoSeleccionado === 'exento')
            <div class="{{ $estadoSeleccionado ? 'col-md-12' : 'col-md-4' }}">
                <div class="alert alert-secondary mb-0">Exentos: <strong>{{ $resumen['exento'] }}</strong></div>
            </div>
        @endif
        @if($estadoSeleccionado === 'ausencia')
            <div class="col-md-12">
                <div class="alert alert-dark mb-0">Ausencias: <strong>{{ $resumen['ausencia'] }}</strong></div>
            </div>
        @endif
    </div>

    @if($alumnos->isEmpty())
        <div class="alert alert-info">No hay alumnos activos para ese mes.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Liga</th>
                        <th>Alta</th>
                        <th>Estado del mes filtrado</th>
                        <th>Meses pendientes</th>
                        <th>Gestionar pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alumnos as $alumno)
                        @php
                            $mesesPendientes = collect($alumno->mesesPendientesHasta($mesSeleccionado))->values();
                            $resumenPendientes = $alumno->resumenMesesPendientesHasta($mesSeleccionado);
                            $mesesDisponibles = collect($mesesPendientes->all())
                                ->sortBy(fn ($mes) => $mes->format('Y-m'))
                                ->values();
                            $mesFormulario = old('mes') && old('alumno_id') == $alumno->id
                                ? \Carbon\Carbon::createFromFormat('Y-m', old('mes'))->startOfMonth()
                                : $mesesPendientes->first();
                            $pagoMes = $mesFormulario ? $alumno->pagoParaMes($mesFormulario) : null;
                            $estadoMes = $mesFormulario ? $alumno->estadoPagoMes($mesFormulario) : 'pendiente';
                            $estadoMesFiltrado = $alumno->estadoPagoMes($mesSeleccionado);
                            $ultimoPago = $alumno->ultimoPagoRegistrado();
                            $mesesFormulario = $mesesDisponibles->map(function ($mes) use ($alumno) {
                                $pago = $alumno->pagoParaMes($mes);

                                return [
                                    'key' => $mes->format('Y-m'),
                                    'estado' => $alumno->estadoPagoMes($mes),
                                    'fecha_pago' => $pago?->fecha_pago?->format('Y-m-d') ?? '',
                                    'importe' => $pago?->importe !== null ? (string) $pago->importe : '',
                                    'observaciones' => $pago?->observaciones ?? '',
                                ];
                            })->keyBy('key');
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('alumnos.show', $alumno) }}">{{ $alumno->nombre }} {{ $alumno->apellidos }}</a>
                                <div class="small text-muted mt-1">
                                    @if($mesesPendientes->isEmpty())
                                        Sin deuda acumulada
                                    @else
                                        {{ $mesesPendientes->count() }} mes(es) pendiente(s)
                                    @endif
                                </div>
                                <div class="small text-muted">
                                    Último pago:
                                    @if($ultimoPago)
                                        {{ $ultimoPago->mes->locale('es')->translatedFormat('F Y') }}
                                        @if($ultimoPago->fecha_pago)
                                            el {{ $ultimoPago->fecha_pago->format('d/m/Y') }}
                                        @endif
                                    @else
                                        sin registros
                                    @endif
                                </div>
                            </td>
                            <td>{{ ucfirst($alumno->liga) }}</td>
                            <td>{{ $alumno->fecha_alta?->format('d/m/Y') ?? '—' }}</td>
                            <td>
                                @if($estadoMesFiltrado === 'pagado')
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($estadoMesFiltrado === 'exento')
                                    <span class="badge bg-secondary">Exento</span>
                                @elseif($estadoMesFiltrado === 'ausencia')
                                    <span class="badge bg-dark-subtle text-dark">Ausencia</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @endif
                            </td>
                            <td>
                                @if($mesesPendientes->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($resumenPendientes as $item)
                                            <span class="badge bg-warning text-dark">{{ $item['label'] }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-success">Al día</span>
                                @endif
                            </td>
                            <td>
                                @if($mesesPendientes->isEmpty())
                                    <span class="text-muted">Sin gestión pendiente</span>
                                @else
                                    <div class="border rounded p-2 bg-body-tertiary">
                                        <form action="{{ route('pagos.store') }}" method="POST" class="pago-form d-flex flex-column gap-2" data-meses='@json($mesesFormulario)'>
                                            @csrf
                                            <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">

                                            <div>
                                                <label class="form-label small mb-1">Mes pendiente</label>
                                                <select name="mes" class="form-select form-select-sm pago-mes">
                                                    @foreach($mesesDisponibles as $mesDisponible)
                                                        <option value="{{ $mesDisponible->format('Y-m') }}" {{ $mesFormulario->format('Y-m') === $mesDisponible->format('Y-m') ? 'selected' : '' }}>
                                                            {{ $mesDisponible->locale('es')->translatedFormat('F Y') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="d-flex gap-2 align-items-end">
                                                <div class="flex-grow-1">
                                                    <label class="form-label small mb-1">Estado</label>
                                                    <select name="estado" class="form-select form-select-sm pago-estado">
                                                        <option value="pendiente" {{ $estadoMes === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                        <option value="pagado" {{ $estadoMes === 'pagado' ? 'selected' : '' }}>Pagado</option>
                                                        <option value="exento" {{ $estadoMes === 'exento' ? 'selected' : '' }}>Exento</option>
                                                        <option value="ausencia" {{ $estadoMes === 'ausencia' ? 'selected' : '' }}>Ausencia</option>
                                                    </select>
                                                </div>
                                                <button class="btn btn-sm btn-primary px-3">Guardar</button>
                                            </div>

                                            <div class="pago-campos-fecha{{ $estadoMes !== 'pagado' ? ' d-none' : '' }}">
                                                <label class="form-label small mb-1">Fecha de pago</label>
                                                <input type="date" name="fecha_pago" class="form-control form-control-sm pago-fecha" value="{{ optional($pagoMes?->fecha_pago)->format('Y-m-d') }}" {{ $estadoMes !== 'pagado' ? 'disabled' : '' }}>
                                            </div>
                                            <div class="pago-campos-importe{{ $estadoMes !== 'pagado' ? ' d-none' : '' }}">
                                                <label class="form-label small mb-1">Importe</label>
                                                <input type="number" step="0.01" min="0" name="importe" class="form-control form-control-sm pago-importe" placeholder="Importe" value="{{ $pagoMes?->importe }}" {{ $estadoMes !== 'pagado' ? 'disabled' : '' }}>
                                            </div>
                                            <div class="pago-campos-observaciones{{ !in_array($estadoMes, ['pagado', 'exento']) ? ' d-none' : '' }}">
                                                <label class="form-label small mb-1">Observaciones</label>
                                                <input type="text" name="observaciones" class="form-control form-control-sm pago-observaciones" placeholder="Observaciones" value="{{ $pagoMes?->observaciones }}" {{ !in_array($estadoMes, ['pagado', 'exento']) ? 'disabled' : '' }}>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $alumnos->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const hoy = new Date().toISOString().slice(0, 10);
    const filtrosForm = document.getElementById('pagos-filtros-form');
    const filtrosAutoSubmit = filtrosForm?.querySelectorAll('input[name="mes"], select[name="liga"], select[name="estado"]') ?? [];
    const filtroBusqueda = filtrosForm?.querySelector('input[name="q"]');
    let filtroBusquedaTimeout;

    function submitFiltros() {
        if (filtrosForm) {
            filtrosForm.requestSubmit();
        }
    }

    function syncPagoForm(form) {
        const meses = JSON.parse(form.dataset.meses || '{}');
        const mesSelect = form.querySelector('.pago-mes');
        const estadoSelect = form.querySelector('.pago-estado');
        const fechaInput = form.querySelector('.pago-fecha');
        const importeInput = form.querySelector('.pago-importe');
        const observacionesInput = form.querySelector('.pago-observaciones');
        const data = meses[mesSelect.value];

        if (!data) {
            return;
        }

        estadoSelect.value = data.estado || 'pendiente';
        fechaInput.value = data.fecha_pago || '';
        importeInput.value = data.importe || '';
        observacionesInput.value = data.observaciones || '';
        togglePagoFields(form);
    }

    function togglePagoFields(form) {
        const estadoSelect = form.querySelector('.pago-estado');
        const fechaInput = form.querySelector('.pago-fecha');
        const importeInput = form.querySelector('.pago-importe');
        const observacionesInput = form.querySelector('.pago-observaciones');
        const fechaWrap = form.querySelector('.pago-campos-fecha');
        const importeWrap = form.querySelector('.pago-campos-importe');
        const observacionesWrap = form.querySelector('.pago-campos-observaciones');
        const esPagado = estadoSelect.value === 'pagado';
        const permiteObservaciones = ['pagado', 'exento'].includes(estadoSelect.value);

        fechaWrap?.classList.toggle('d-none', !esPagado);
        importeWrap?.classList.toggle('d-none', !esPagado);
        observacionesWrap?.classList.toggle('d-none', !permiteObservaciones);

        fechaInput.disabled = !esPagado;
        importeInput.disabled = !esPagado;
        observacionesInput.disabled = !permiteObservaciones;

        if (!esPagado) {
            fechaInput.value = '';
            importeInput.value = '';
        }

        if (!permiteObservaciones) {
            observacionesInput.value = '';
        }
    }

    document.querySelectorAll('.pago-form').forEach((form) => {
        const mesSelect = form.querySelector('.pago-mes');
        const estadoSelect = form.querySelector('.pago-estado');
        const fechaInput = form.querySelector('.pago-fecha');

        mesSelect.addEventListener('change', () => syncPagoForm(form));

        estadoSelect.addEventListener('change', () => {
            if (estadoSelect.value === 'pagado' && !fechaInput.value) {
                fechaInput.value = hoy;
            }

            togglePagoFields(form);
        });

        togglePagoFields(form);
    });

    filtrosAutoSubmit.forEach((input) => {
        input.addEventListener('change', submitFiltros);
    });

    if (filtroBusqueda) {
        filtroBusqueda.addEventListener('input', () => {
            clearTimeout(filtroBusquedaTimeout);
            filtroBusquedaTimeout = setTimeout(submitFiltros, 350);
        });
    }
});
</script>
@endsection
