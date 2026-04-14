<?php

namespace Database\Factories;

use App\Models\Alumno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alumno>
 */
class AlumnoFactory extends Factory
{
    protected $model = Alumno::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName() . ' ' . fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-16 years', '-7 years')->format('Y-m-d'),
            'fecha_alta' => fake()->dateTimeBetween('-12 months', 'now')->format('Y-m-d'),
            'liga' => fake()->randomElement(['local', 'infantil']),
        ];
    }

    public function local(): static
    {
        return $this->state(fn () => [
            'liga' => 'local',
            'fecha_nacimiento' => fake()->dateTimeBetween('-18 years', '-13 years')->format('Y-m-d'),
            'fecha_alta' => fake()->dateTimeBetween('-12 months', 'now')->format('Y-m-d'),
        ]);
    }

    public function infantil(): static
    {
        return $this->state(fn () => [
            'liga' => 'infantil',
            'fecha_nacimiento' => fake()->dateTimeBetween('-12 years', '-7 years')->format('Y-m-d'),
            'fecha_alta' => fake()->dateTimeBetween('-12 months', 'now')->format('Y-m-d'),
        ]);
    }
}
