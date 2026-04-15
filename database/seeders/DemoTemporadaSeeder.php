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
        $locales = Alumno::query()->where('liga', 'local')->orderBy('fecha_alta')->orderBy('apellidos')->orderBy('nombre')->get();
        $infantiles = Alumno::query()->where('liga', 'infantil')->orderBy('fecha_alta')->orderBy('apellidos')->orderBy('nombre')->get();

        $temporadas = [
            ['nombre' => 'ENERO-ABRIL 2024', 'inicio' => '2024-01-01', 'fin' => '2024-04-30', 'rondas' => null],
            ['nombre' => 'ABRIL-JUNIO 2024', 'inicio' => '2024-05-01', 'fin' => '2024-06-30', 'rondas' => null],
            ['nombre' => 'SEPTIEMBRE-ENERO 2025', 'inicio' => '2024-09-01', 'fin' => '2025-01-31', 'rondas' => null],
            ['nombre' => 'ENERO-ABRIL 2025', 'inicio' => '2025-02-01', 'fin' => '2025-04-30', 'rondas' => null],
            ['nombre' => 'ABRIL-JUNIO 2025', 'inicio' => '2025-05-01', 'fin' => '2025-06-30', 'rondas' => null],
            ['nombre' => 'SEPTIEMBRE-ENERO 2026', 'inicio' => '2025-09-01', 'fin' => '2026-01-31', 'rondas' => null],
            ['nombre' => 'ENERO-ABRIL 2026', 'inicio' => '2026-02-01', 'fin' => '2026-03-31', 'rondas' => null],
            ['nombre' => 'ABRIL-JUNIO 2026', 'inicio' => '2026-04-01', 'fin' => '2026-06-30', 'rondas' => 4],
        ];

        foreach ($temporadas as $config) {
            $temporada = Temporada::create([
                'nombre' => $config['nombre'],
                'fecha_inicio' => $config['inicio'],
                'fecha_fin' => $config['fin'],
            ]);

            $fechaFin = Carbon::parse($config['fin']);
            $activosLocal = $locales->filter(fn (Alumno $alumno) => $alumno->fechaAltaCobro()->lte($fechaFin))->values();
            $activosInfantil = $infantiles->filter(fn (Alumno $alumno) => $alumno->fechaAltaCobro()->lte($fechaFin))->values();

            $temporada->alumnos()->sync($activosLocal->concat($activosInfantil)->pluck('id')->all());

            $this->crearEnfrentamientosLiga($temporada, 'local', $activosLocal, Carbon::parse($config['inicio'])->next(Carbon::SATURDAY), $config['rondas']);
            $this->crearEnfrentamientosLiga($temporada, 'infantil', $activosInfantil, Carbon::parse($config['inicio'])->next(Carbon::SUNDAY), $config['rondas']);

            $this->recalcularClasificacion($temporada, $activosLocal, $activosInfantil);
        }

        $this->crearPagosDemo($locales->concat($infantiles)->values());
    }

    private function crearEnfrentamientosLiga(Temporada $temporada, string $liga, $alumnos, Carbon $fechaInicio, ?int $limiteRondas = null): void
    {
        $ids = $alumnos->pluck('id')->values()->all();

        if (count($ids) < 2) {
            return;
        }

        $rondas = $this->generarRondas($ids);

        if ($limiteRondas !== null) {
            $rondas = array_slice($rondas, 0, $limiteRondas);
        }

        $resultados = ['blancas', 'negras', 'tablas', 'blancas', 'negras', 'tablas'];

        foreach ($rondas as $indiceRonda => $ronda) {
            $fechaRonda = $fechaInicio->copy()->addWeeks($indiceRonda);

            foreach ($ronda as $indiceMesa => [$alumno1Id, $alumno2Id]) {
                Enfrentamiento::create([
                    'temporada_id' => $temporada->id,
                    'liga' => $liga,
                    'alumno1_id' => $alumno1Id,
                    'alumno2_id' => $alumno2Id,
                    'resultado' => $resultados[($indiceRonda + $indiceMesa + $temporada->id) % count($resultados)],
                    'fecha' => $fechaRonda->copy()->addMinutes($indiceMesa * 20),
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
                $a = $jugadores[$i];
                $b = $jugadores[$total - 1 - $i];

                if ($a !== null && $b !== null) {
                    $parejas[] = $ronda % 2 === 0
                        ? [$a, $b]
                        : [$b, $a];
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

    private function recalcularClasificacion(Temporada $temporada, $activosLocal, $activosInfantil): void
    {
        $puntos = [];

        foreach ($activosLocal->concat($activosInfantil) as $alumno) {
            $puntos[$alumno->id] = 0;
        }

        foreach ($temporada->enfrentamientos as $enfrentamiento) {
            if ($enfrentamiento->resultado === 'blancas') {
                $puntos[$enfrentamiento->alumno1_id] += 3;
                $puntos[$enfrentamiento->alumno2_id] += 1;
            } elseif ($enfrentamiento->resultado === 'negras') {
                $puntos[$enfrentamiento->alumno1_id] += 1;
                $puntos[$enfrentamiento->alumno2_id] += 3;
            } elseif ($enfrentamiento->resultado === 'tablas') {
                $puntos[$enfrentamiento->alumno1_id] += 2;
                $puntos[$enfrentamiento->alumno2_id] += 2;
            }
        }

        arsort($puntos);
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

    private function crearPagosDemo($alumnos): void
    {
        $inicioBase = Carbon::parse('2024-04-01');
        $mesActual = Carbon::parse('2026-04-01');

        foreach ($alumnos->values() as $indice => $alumno) {
            $cursor = $alumno->fechaAltaCobro()->copy()->startOfMonth();

            if ($cursor->lt($inicioBase)) {
                $cursor = $inicioBase->copy();
            }

            while ($cursor->lte($mesActual)) {
                $estado = $this->estadoPagoDemo($alumno, $indice, $cursor, $mesActual);

                Pago::create([
                    'alumno_id' => $alumno->id,
                    'mes' => $cursor->toDateString(),
                    'estado' => $estado,
                    'fecha_pago' => $estado === 'pagado'
                        ? $cursor->copy()->day(min(5 + (($alumno->id + $indice) % 6), $cursor->daysInMonth))->toDateString()
                        : null,
                    'importe' => $estado === 'pagado' ? 15 : null,
                    'observaciones' => $estado === 'ausencia' ? 'No asistió este mes.' : null,
                ]);

                $cursor->addMonth();
            }
        }
    }

    private function estadoPagoDemo(Alumno $alumno, int $indice, Carbon $mes, Carbon $mesActual): string
    {
        if ($mes->equalTo($mesActual)) {
            return (($indice + $alumno->id) % 5 === 0) ? 'pendiente' : 'pagado';
        }

        $hash = crc32($alumno->id . '-' . $mes->format('Y-m'));

        if ($hash % 11 === 0) {
            return 'ausencia';
        }

        return 'pagado';
    }
}