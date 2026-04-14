<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellidos',
        'fecha_nacimiento',
        'fecha_alta',
        'liga',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_alta' => 'date',
    ];

    public function clasificacions()
    {
        return $this->hasMany(Clasificacion::class);
    }

    public function enfrentamientosComoAlumno1()
    {
        return $this->hasMany(Enfrentamiento::class, 'alumno1_id');
    }

    public function enfrentamientosComoAlumno2()
    {
        return $this->hasMany(Enfrentamiento::class, 'alumno2_id');
    }

    public function temporadas()
    {
        return $this->belongsToMany(Temporada::class, 'temporada_alumno');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function fechaAltaCobro(): Carbon
    {
        if ($this->fecha_alta instanceof Carbon) {
            return $this->fecha_alta->copy();
        }

        if ($this->fecha_alta) {
            return Carbon::parse($this->fecha_alta);
        }

        return ($this->created_at instanceof Carbon ? $this->created_at->copy() : now())->startOfDay();
    }

    public function pagoParaMes(Carbon|string $mes): ?Pago
    {
        $mes = $mes instanceof Carbon ? $mes->copy()->startOfMonth() : Carbon::parse($mes)->startOfMonth();

        if ($this->relationLoaded('pagos')) {
            return $this->pagos->first(function ($pago) use ($mes) {
                return $pago->mes && $pago->mes->isSameDay($mes);
            });
        }

        return $this->pagos()->whereDate('mes', $mes->toDateString())->first();
    }

    public function estadoPagoMes(Carbon|string $mes): string
    {
        $mes = $mes instanceof Carbon ? $mes->copy()->startOfMonth() : Carbon::parse($mes)->startOfMonth();

        if ($mes->lt($this->fechaAltaCobro()->copy()->startOfMonth())) {
            return 'exento';
        }

        return $this->pagoParaMes($mes)?->estado ?? 'pendiente';
    }

    public function mesesPendientesHasta(?Carbon $hasta = null): array
    {
        $hasta = ($hasta ?? now())->copy()->startOfMonth();
        $inicio = $this->fechaAltaCobro()->copy()->startOfMonth();

        if ($inicio->gt($hasta)) {
            return [];
        }

        $pagos = $this->relationLoaded('pagos')
            ? $this->pagos
            : $this->pagos()->whereDate('mes', '<=', $hasta->copy()->endOfMonth())->get();

        $estadosPorMes = $pagos->mapWithKeys(function ($pago) {
            return [$pago->mes->format('Y-m') => $pago->estado];
        });

        $pendientes = [];
        $cursor = $inicio->copy();

        while ($cursor->lte($hasta)) {
            $estado = $estadosPorMes[$cursor->format('Y-m')] ?? 'pendiente';

            if ($estado === 'pendiente') {
                $pendientes[] = $cursor->copy();
            }

            $cursor->addMonth();
        }

        return $pendientes;
    }

    public function totalPagosPendientesHasta(?Carbon $hasta = null): int
    {
        return count($this->mesesPendientesHasta($hasta));
    }

    public function tienePagosPendientesHasta(?Carbon $hasta = null): bool
    {
        return $this->totalPagosPendientesHasta($hasta) > 0;
    }

    public function resumenMesesPendientesHasta(?Carbon $hasta = null): array
    {
        $mesesPendientes = collect($this->mesesPendientesHasta($hasta));

        if ($mesesPendientes->isEmpty()) {
            return [];
        }

        return $mesesPendientes
            ->groupBy(fn (Carbon $mes) => $mes->year)
            ->flatMap(function ($meses, $year) {
                $meses = collect($meses)->sortBy(fn (Carbon $mes) => $mes->month)->values();

                if ($meses->count() === 12 && $meses->pluck('month')->values()->all() === range(1, 12)) {
                    return [[
                        'label' => (string) $year,
                        'full_year' => true,
                    ]];
                }

                return $meses->map(function (Carbon $mes) {
                    return [
                        'label' => $mes->locale('es')->translatedFormat('M Y'),
                        'full_year' => false,
                    ];
                });
            })
            ->values()
            ->all();
    }

    public function ultimoPagoRegistrado(): ?Pago
    {
        if ($this->relationLoaded('pagos')) {
            return $this->pagos
                ->where('estado', 'pagado')
                ->sortByDesc(fn ($pago) => $pago->mes?->format('Y-m-d'))
                ->first();
        }

        return $this->pagos()
            ->where('estado', 'pagado')
            ->orderBy('mes', 'desc')
            ->first();
    }
}
