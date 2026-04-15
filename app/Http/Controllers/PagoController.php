<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $mesSeleccionado = $request->filled('mes')
            ? Carbon::createFromFormat('Y-m', $request->mes)->startOfMonth()
            : now()->startOfMonth();

        $query = Alumno::with(['pagos' => function ($query) use ($mesSeleccionado) {
            $query->whereDate('mes', '<=', $mesSeleccionado->copy()->endOfMonth())
                ->orderBy('mes', 'desc');
        }]);

        if ($request->filled('liga') && in_array($request->liga, ['local', 'infantil'])) {
            $query->where('liga', $request->liga);
        }

        if ($request->filled('q')) {
            $search = trim($request->q);

            $query->where(function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%");
            });
        }

        $alumnos = $query
            ->where(function ($query) use ($mesSeleccionado) {
                $query->whereNull('fecha_alta')
                    ->orWhereDate('fecha_alta', '<=', $mesSeleccionado->copy()->endOfMonth());
            })
            ->orderBy('liga')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        if ($request->filled('estado') && in_array($request->estado, ['pagado', 'pendiente', 'exento', 'ausencia'])) {
            $estadoFiltro = $request->estado;

            $alumnos = $alumnos->filter(function ($alumno) use ($estadoFiltro, $mesSeleccionado) {
                return $alumno->estadoPagoMes($mesSeleccionado) === $estadoFiltro;
            })->values();
        } else {
            $alumnos = $alumnos->filter(function ($alumno) use ($mesSeleccionado) {
                $estadoMes = $alumno->estadoPagoMes($mesSeleccionado);

                if ($estadoMes !== 'ausencia') {
                    return true;
                }

                return $alumno->tienePagosPendientesHasta($mesSeleccionado);
            })->values();
        }

        $resumen = [
            'pagado' => 0,
            'pendiente' => 0,
            'exento' => 0,
            'ausencia' => 0,
        ];

        foreach ($alumnos as $alumno) {
            $estado = $alumno->estadoPagoMes($mesSeleccionado);

            if (isset($resumen[$estado])) {
                $resumen[$estado]++;
            }
        }

        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $total = $alumnos->count();
        $items = $alumnos->slice(($page - 1) * $perPage, $perPage)->values();

        $alumnos = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('pagos.index', compact('alumnos', 'mesSeleccionado', 'resumen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'mes' => 'required|date_format:Y-m',
            'estado' => 'required|in:pagado,pendiente,exento,ausencia',
            'fecha_pago' => 'nullable|date',
            'importe' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $mes = Carbon::createFromFormat('Y-m', $validated['mes'])->startOfMonth();
        $alumno = Alumno::findOrFail($validated['alumno_id']);

        if ($mes->lt($alumno->fechaAltaCobro()->copy()->startOfMonth())) {
            return back()->with('error', 'No se puede registrar un pago anterior a la fecha de alta del alumno.');
        }

        $datosPago = [
            'estado' => $validated['estado'],
            'fecha_pago' => $validated['estado'] === 'pagado'
                ? ($validated['fecha_pago'] ?? now()->toDateString())
                : null,
            'importe' => $validated['estado'] === 'pagado' ? ($validated['importe'] ?? null) : null,
            'observaciones' => in_array($validated['estado'], ['pagado', 'exento'], true)
                ? ($validated['observaciones'] ?? null)
                : null,
        ];

        $pago = Pago::query()
            ->where('alumno_id', $alumno->id)
            ->whereDate('mes', $mes->toDateString())
            ->first();

        if ($pago) {
            $pago->update($datosPago);
        } else {
            Pago::create([
                'alumno_id' => $alumno->id,
                'mes' => $mes->toDateString(),
                ...$datosPago,
            ]);
        }

        return back()->with('success', 'Pago actualizado correctamente.');
    }

    public function update(Request $request, Pago $pago)
    {
        $validated = $request->validate([
            'estado' => 'required|in:pagado,pendiente,exento,ausencia',
            'fecha_pago' => 'nullable|date',
            'importe' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $pago->update([
            'estado' => $validated['estado'],
            'fecha_pago' => $validated['estado'] === 'pagado'
                ? ($validated['fecha_pago'] ?? $pago->fecha_pago?->toDateString() ?? now()->toDateString())
                : null,
            'importe' => $validated['estado'] === 'pagado' ? ($validated['importe'] ?? null) : null,
            'observaciones' => in_array($validated['estado'], ['pagado', 'exento'], true)
                ? ($validated['observaciones'] ?? null)
                : null,
        ]);

        return back()->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy(Pago $pago)
    {
        $pago->delete();

        return back()->with('success', 'Pago eliminado correctamente.');
    }
}
