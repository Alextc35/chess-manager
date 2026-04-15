<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Clasificacion;
use App\Models\Enfrentamiento;
use App\Models\Pago;
use App\Models\Temporada;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        Clasificacion::query()->delete();
        Enfrentamiento::query()->delete();
        Pago::query()->delete();
        DB::table('temporada_alumno')->delete();
        Temporada::query()->delete();
        Alumno::query()->delete();

        $this->call([
            AlumnoSeeder::class,
            DemoTemporadaSeeder::class,
        ]);
    }
}