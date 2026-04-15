<?php

namespace Database\Seeders;

use App\Models\Alumno;
use Illuminate\Database\Seeder;

class AlumnoSeeder extends Seeder
{
    public function run(): void
    {
        $alumnos = [
            ['nombre' => 'Carlos', 'apellidos' => 'Martin Ruiz', 'fecha_nacimiento' => '2004-02-14', 'fecha_alta' => '2024-01-05', 'liga' => 'local'],
            ['nombre' => 'David', 'apellidos' => 'Serrano Gil', 'fecha_nacimiento' => '2003-07-03', 'fecha_alta' => '2024-01-05', 'liga' => 'local'],
            ['nombre' => 'Javier', 'apellidos' => 'Lopez Vega', 'fecha_nacimiento' => '2002-11-21', 'fecha_alta' => '2024-01-05', 'liga' => 'local'],
            ['nombre' => 'Mario', 'apellidos' => 'Fernandez Soto', 'fecha_nacimiento' => '2001-05-09', 'fecha_alta' => '2024-01-12', 'liga' => 'local'],
            ['nombre' => 'Pablo', 'apellidos' => 'Navarro Diaz', 'fecha_nacimiento' => '2003-09-18', 'fecha_alta' => '2024-01-12', 'liga' => 'local'],
            ['nombre' => 'Adrian', 'apellidos' => 'Moreno Castro', 'fecha_nacimiento' => '2002-01-27', 'fecha_alta' => '2024-02-02', 'liga' => 'local'],
            ['nombre' => 'Sergio', 'apellidos' => 'Ortega Leon', 'fecha_nacimiento' => '2004-04-30', 'fecha_alta' => '2024-02-02', 'liga' => 'local'],
            ['nombre' => 'Alberto', 'apellidos' => 'Prieto Ramos', 'fecha_nacimiento' => '2000-12-12', 'fecha_alta' => '2024-02-16', 'liga' => 'local'],
            ['nombre' => 'Daniel', 'apellidos' => 'Herrera Moya', 'fecha_nacimiento' => '2003-03-25', 'fecha_alta' => '2024-03-01', 'liga' => 'local'],
            ['nombre' => 'Miguel', 'apellidos' => 'Sanchez Marin', 'fecha_nacimiento' => '2002-08-07', 'fecha_alta' => '2024-03-15', 'liga' => 'local'],
            ['nombre' => 'Raul', 'apellidos' => 'Iglesias Pardo', 'fecha_nacimiento' => '2001-10-16', 'fecha_alta' => '2024-09-01', 'liga' => 'local'],
            ['nombre' => 'Ivan', 'apellidos' => 'Cano Rubio', 'fecha_nacimiento' => '2004-06-01', 'fecha_alta' => '2024-09-15', 'liga' => 'local'],
            ['nombre' => 'Marcos', 'apellidos' => 'Chamorro Ignacio', 'fecha_nacimiento' => '2002-02-23', 'fecha_alta' => '2025-01-10', 'liga' => 'local'],
            ['nombre' => 'Tomas', 'apellidos' => 'Lorenzo Peña', 'fecha_nacimiento' => '2003-12-05', 'fecha_alta' => '2025-04-08', 'liga' => 'local'],
            ['nombre' => 'Alejandro', 'apellidos' => 'Tellez Corona', 'fecha_nacimiento' => '2001-07-21', 'fecha_alta' => '2025-09-03', 'liga' => 'local'],
            ['nombre' => 'Hector', 'apellidos' => 'Garrido Soler', 'fecha_nacimiento' => '2005-01-18', 'fecha_alta' => '2026-01-09', 'liga' => 'local'],

            ['nombre' => 'Lucia', 'apellidos' => 'Garcia Torres', 'fecha_nacimiento' => '2014-01-15', 'fecha_alta' => '2024-01-05', 'liga' => 'infantil'],
            ['nombre' => 'Paula', 'apellidos' => 'Jimenez Rivas', 'fecha_nacimiento' => '2013-04-04', 'fecha_alta' => '2024-01-05', 'liga' => 'infantil'],
            ['nombre' => 'Alba', 'apellidos' => 'Molina Perez', 'fecha_nacimiento' => '2012-09-28', 'fecha_alta' => '2024-01-12', 'liga' => 'infantil'],
            ['nombre' => 'Sofia', 'apellidos' => 'Ruiz Calvo', 'fecha_nacimiento' => '2014-06-19', 'fecha_alta' => '2024-01-12', 'liga' => 'infantil'],
            ['nombre' => 'Nora', 'apellidos' => 'Blanco Sanz', 'fecha_nacimiento' => '2013-11-11', 'fecha_alta' => '2024-02-02', 'liga' => 'infantil'],
            ['nombre' => 'Diego', 'apellidos' => 'Rey Pastor', 'fecha_nacimiento' => '2012-03-08', 'fecha_alta' => '2024-02-02', 'liga' => 'infantil'],
            ['nombre' => 'Hugo', 'apellidos' => 'Arias Fuentes', 'fecha_nacimiento' => '2013-07-22', 'fecha_alta' => '2024-02-16', 'liga' => 'infantil'],
            ['nombre' => 'Leo', 'apellidos' => 'Delgado Crespo', 'fecha_nacimiento' => '2014-10-02', 'fecha_alta' => '2024-03-01', 'liga' => 'infantil'],
            ['nombre' => 'Martin', 'apellidos' => 'Vargas Soto', 'fecha_nacimiento' => '2012-12-17', 'fecha_alta' => '2024-03-15', 'liga' => 'infantil'],
            ['nombre' => 'Manuel', 'apellidos' => 'Roman Alonso', 'fecha_nacimiento' => '2013-02-09', 'fecha_alta' => '2024-03-15', 'liga' => 'infantil'],
            ['nombre' => 'Julia', 'apellidos' => 'Cortes Plaza', 'fecha_nacimiento' => '2014-08-13', 'fecha_alta' => '2024-09-01', 'liga' => 'infantil'],
            ['nombre' => 'Valeria', 'apellidos' => 'Benitez Luna', 'fecha_nacimiento' => '2012-05-26', 'fecha_alta' => '2024-09-15', 'liga' => 'infantil'],
            ['nombre' => 'Emma', 'apellidos' => 'Santos Pardo', 'fecha_nacimiento' => '2015-04-01', 'fecha_alta' => '2025-01-10', 'liga' => 'infantil'],
            ['nombre' => 'Gael', 'apellidos' => 'Nuñez Robles', 'fecha_nacimiento' => '2013-09-09', 'fecha_alta' => '2025-04-08', 'liga' => 'infantil'],
            ['nombre' => 'Noa', 'apellidos' => 'Campos Vera', 'fecha_nacimiento' => '2014-11-23', 'fecha_alta' => '2025-09-03', 'liga' => 'infantil'],
            ['nombre' => 'Thiago', 'apellidos' => 'Sanz Medina', 'fecha_nacimiento' => '2015-06-14', 'fecha_alta' => '2026-01-09', 'liga' => 'infantil'],
        ];

        foreach ($alumnos as $alumno) {
            Alumno::create($alumno);
        }
    }
}