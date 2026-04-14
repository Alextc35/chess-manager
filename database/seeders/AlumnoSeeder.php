<?php

namespace Database\Seeders;

use App\Models\Alumno;
use Illuminate\Database\Seeder;

class AlumnoSeeder extends Seeder
{
    public function run(): void
    {
        $alumnos = [
            ['nombre' => 'Carlos', 'apellidos' => 'Martin Ruiz', 'fecha_nacimiento' => '2009-02-14', 'fecha_alta' => '2025-09-01', 'liga' => 'local'],
            ['nombre' => 'David', 'apellidos' => 'Serrano Gil', 'fecha_nacimiento' => '2008-07-03', 'fecha_alta' => '2025-09-01', 'liga' => 'local'],
            ['nombre' => 'Javier', 'apellidos' => 'Lopez Vega', 'fecha_nacimiento' => '2007-11-21', 'fecha_alta' => '2025-09-01', 'liga' => 'local'],
            ['nombre' => 'Mario', 'apellidos' => 'Fernandez Soto', 'fecha_nacimiento' => '2006-05-09', 'fecha_alta' => '2025-09-01', 'liga' => 'local'],
            ['nombre' => 'Pablo', 'apellidos' => 'Navarro Diaz', 'fecha_nacimiento' => '2008-09-18', 'fecha_alta' => '2025-10-01', 'liga' => 'local'],
            ['nombre' => 'Adrian', 'apellidos' => 'Moreno Castro', 'fecha_nacimiento' => '2007-01-27', 'fecha_alta' => '2025-10-01', 'liga' => 'local'],
            ['nombre' => 'Sergio', 'apellidos' => 'Ortega Leon', 'fecha_nacimiento' => '2009-04-30', 'fecha_alta' => '2025-11-01', 'liga' => 'local'],
            ['nombre' => 'Alberto', 'apellidos' => 'Prieto Ramos', 'fecha_nacimiento' => '2005-12-12', 'fecha_alta' => '2025-11-01', 'liga' => 'local'],
            ['nombre' => 'Daniel', 'apellidos' => 'Herrera Moya', 'fecha_nacimiento' => '2008-03-25', 'fecha_alta' => '2025-12-01', 'liga' => 'local'],
            ['nombre' => 'Miguel', 'apellidos' => 'Sanchez Marin', 'fecha_nacimiento' => '2007-08-07', 'fecha_alta' => '2026-01-01', 'liga' => 'local'],
            ['nombre' => 'Raul', 'apellidos' => 'Iglesias Pardo', 'fecha_nacimiento' => '2006-10-16', 'fecha_alta' => '2026-01-01', 'liga' => 'local'],
            ['nombre' => 'Ivan', 'apellidos' => 'Cano Rubio', 'fecha_nacimiento' => '2009-06-01', 'fecha_alta' => '2026-02-01', 'liga' => 'local'],
            ['nombre' => 'Lucia', 'apellidos' => 'Garcia Torres', 'fecha_nacimiento' => '2016-01-15', 'fecha_alta' => '2025-09-01', 'liga' => 'infantil'],
            ['nombre' => 'Paula', 'apellidos' => 'Jimenez Rivas', 'fecha_nacimiento' => '2015-04-04', 'fecha_alta' => '2025-09-01', 'liga' => 'infantil'],
            ['nombre' => 'Alba', 'apellidos' => 'Molina Perez', 'fecha_nacimiento' => '2014-09-28', 'fecha_alta' => '2025-10-01', 'liga' => 'infantil'],
            ['nombre' => 'Sofia', 'apellidos' => 'Ruiz Calvo', 'fecha_nacimiento' => '2016-06-19', 'fecha_alta' => '2025-10-01', 'liga' => 'infantil'],
            ['nombre' => 'Nora', 'apellidos' => 'Blanco Sanz', 'fecha_nacimiento' => '2015-11-11', 'fecha_alta' => '2025-11-01', 'liga' => 'infantil'],
            ['nombre' => 'Diego', 'apellidos' => 'Rey Pastor', 'fecha_nacimiento' => '2014-03-08', 'fecha_alta' => '2025-11-01', 'liga' => 'infantil'],
            ['nombre' => 'Hugo', 'apellidos' => 'Arias Fuentes', 'fecha_nacimiento' => '2015-07-22', 'fecha_alta' => '2025-12-01', 'liga' => 'infantil'],
            ['nombre' => 'Leo', 'apellidos' => 'Delgado Crespo', 'fecha_nacimiento' => '2016-10-02', 'fecha_alta' => '2025-12-01', 'liga' => 'infantil'],
            ['nombre' => 'Martin', 'apellidos' => 'Vargas Soto', 'fecha_nacimiento' => '2014-12-17', 'fecha_alta' => '2026-01-01', 'liga' => 'infantil'],
            ['nombre' => 'Manuel', 'apellidos' => 'Roman Alonso', 'fecha_nacimiento' => '2015-02-09', 'fecha_alta' => '2026-01-01', 'liga' => 'infantil'],
            ['nombre' => 'Julia', 'apellidos' => 'Cortes Plaza', 'fecha_nacimiento' => '2016-08-13', 'fecha_alta' => '2026-02-01', 'liga' => 'infantil'],
            ['nombre' => 'Valeria', 'apellidos' => 'Benitez Luna', 'fecha_nacimiento' => '2014-05-26', 'fecha_alta' => '2026-02-01', 'liga' => 'infantil'],
        ];

        foreach ($alumnos as $alumno) {
            Alumno::updateOrCreate(
                [
                    'nombre' => $alumno['nombre'],
                    'apellidos' => $alumno['apellidos'],
                ],
                $alumno
            );
        }
    }
}
