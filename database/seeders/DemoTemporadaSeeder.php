<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Clasificacion;
use App\Models\Enfrentamiento;
use App\Models\Pago;
use App\Models\Temporada;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoTemporadaSeeder extends Seeder
{
    public function run(): void
    {
        $temporada = Temporada::updateOrCreate(
            ['nombre' => 'Enero-Abril 2026'],
            [
                'fecha_inicio' => '2026-01-01',
                'fecha_fin' => '2026-04-30',
            ]
        );

        $alumnosLocal = Alumno::query()
            ->where('liga', 'local')
            ->whereDate('fecha_alta', '<=', '2026-01-31')
            ->orderBy('fecha_alta')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->take(8)
            ->get();

        $alumnosInfantil = Alumno::query()
            ->where('liga', 'infantil')
            ->whereDate('fecha_alta', '<=', '2026-01-31')
            ->orderBy('fecha_alta')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->take(8)
            ->get();

        $alumnosTemporada = $alumnosLocal
            ->concat($alumnosInfantil)
            ->pluck('id')
            ->all();

        $temporada->alumnos()->sync($alumnosTemporada);
        $temporada->enfrentamientos()->delete();
        $temporada->clasificacions()->delete();
        Pago::query()->whereIn('alumno_id', $alumnosTemporada)->delete();

        $this->crearEnfrentamientosLiga($temporada, 'local', $alumnosLocal, Carbon::parse('2026-01-10'));
        $this->crearEnfrentamientosLiga($temporada, 'infantil', $alumnosInfantil, Carbon::parse('2026-01-11'));
        $this->crearPagosDemo($alumnosLocal, $alumnosInfantil);

        $this->recalcularClasificacion($temporada);
    }

    private function crearEnfrentamientosLiga(Temporada $temporada, string $liga, $alumnos, Carbon $fechaInicio): void
    {
        $ids = $alumnos->pluck('id')->values()->all();

        if (count($ids) < 2) {
            return;
        }

        $rondas = $this->generarRondas($ids);
        $resultados = ['blancas', 'negras', 'tablas', 'blancas', 'tablas', 'negras'];

        foreach ($rondas as $indiceRonda => $ronda) {
            $fechaRonda = $fechaInicio->copy()->addWeeks($indiceRonda);

            foreach ($ronda as $indiceMesa => [$alumno1Id, $alumno2Id]) {
                Enfrentamiento::create([
                    'temporada_id' => $temporada->id,
                    'liga' => $liga,
                    'alumno1_id' => $alumno1Id,
                    'alumno2_id' => $alumno2Id,
                    'resultado' => $resultados[($indiceRonda + $indiceMesa) % count($resultados)],
                    'fecha' => $fechaRonda->copy()->addMinutes($indiceMesa * 15),
                ]);
            }
        }
    }

    private function generarRondas(array $jugadores): array
    {
        $jugadores = array_values($jugadores);

        if (count($jugadores) % 2 !== 0) {
            $jugadores[] = null;
        }

        $total = count($jugadores);
        $rondas = [];

        for ($ronda = 0; $ronda < $total - 1; $ronda++) {
            $parejas = [];

            for ($i = 0; $i < $total / 2; $i++) {
                $local = $jugadores[$i];
                $visitante = $jugadores[$total - 1 - $i];

                if ($local !== null && $visitante !== null) {
                    $parejas[] = $ronda % 2 === 0
                        ? [$local, $visitante]
                        : [$visitante, $local];
                }
            }

            $rondas[] = $parejas;

            $fijo = array_shift($jugadores);
            $ultimo = array_pop($jugadores);
            array_unshift($jugadores, $fijo);
            array_splice($jugadores, 1, 0, [$ultimo]);
        }

        return $rondas;
    }

    private function recalcularClasificacion(Temporada $temporada): void
    {
        $puntos = $temporada->fresh('enfrentamientos')->calcularClasificacion();
        $posicion = 1;

        foreach ($puntos as $alumnoId => $puntuacion) {
            Clasificacion::create([
                'temporada_id' => $temporada->id,
                'alumno_id' => $alumnoId,
                'puntos' => $puntuacion,
                'posicion' => $posicion++,
            ]);
        }
    }

    private function crearPagosDemo($alumnosLocal, $alumnosInfantil): void
    {
        $patrones = [
            ['pagado', 'pagado', 'pagado', 'pagado'],
            ['pagado', 'pagado', 'pagado', 'pendiente'],
            ['pagado', 'ausencia', 'pagado', 'pagado'],
            ['pagado', 'pagado', 'ausencia', 'ausencia'],
            ['pagado', 'pagado', 'pagado', 'pendiente'],
            ['pagado', 'exento', 'pagado', 'pagado'],
            ['pagado', 'pagado', 'ausencia', 'pagado'],
            ['pagado', 'pagado', 'pagado', 'pagado'],
        ];

        $alumnos = $alumnosLocal->concat($alumnosInfantil)->values();

        foreach ($alumnos as $indice => $alumno) {
            $patron = $patrones[$indice % count($patrones)];
            $this->registrarPagosAlumno($alumno, $patron);
        }
    }

    private function registrarPagosAlumno(Alumno $alumno, array $patron): void
    {
        $meses = [
            Carbon::parse('2026-01-01'),
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-03-01'),
            Carbon::parse('2026-04-01'),
        ];

        foreach ($meses as $indice => $mes) {
            if ($mes->lt($alumno->fechaAltaCobro()->copy()->startOfMonth())) {
                continue;
            }

            $estado = $patron[$indice] ?? 'pendiente';
            $datos = [
                'alumno_id' => $alumno->id,
                'mes' => $mes->toDateString(),
                'estado' => $estado,
                'fecha_pago' => null,
                'importe' => null,
                'observaciones' => null,
            ];

            if ($estado === 'pagado') {
                $datos['fecha_pago'] = $mes->copy()->day(min(5 + ($alumno->id % 6), $mes->daysInMonth))->toDateString();
                $datos['importe'] = 15;
            }

            if ($estado === 'ausencia') {
                $datos['observaciones'] = 'Ausencia registrada en la demo';
            }

            if ($estado === 'exento') {
                $datos['observaciones'] = 'Mes exento en la demo';
            }

            Pago::create($datos);
        }
    }
}
