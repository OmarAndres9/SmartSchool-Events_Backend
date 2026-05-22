<?php

namespace Database\Factories;

use App\Models\Materia;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'estudiante_id' => User::factory(),
            'materia_id' => Materia::factory(),
            'periodo_id' => Periodo::factory(),
            'calificacion' => fake()->randomFloat(2, 1, 5),
            'registrado_por' => User::factory(),
        ];
    }

    public function aprobada(): static
    {
        return $this->state(fn () => ['calificacion' => fake()->randomFloat(2, 3, 5)]);
    }

    public function reprobada(): static
    {
        return $this->state(fn () => ['calificacion' => fake()->randomFloat(2, 1, 2.9)]);
    }
}
