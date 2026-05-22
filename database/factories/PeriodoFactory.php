<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodoFactory extends Factory
{
    public function definition(): array
    {
        $anio = fake()->numberBetween(2025, 2027);
        $semestre = fake()->randomElement([1, 2]);
        $inicio = $semestre === 1 ? "{$anio}-02-01" : "{$anio}-08-01";
        $fin = $semestre === 1 ? "{$anio}-06-30" : "{$anio}-12-15";

        return [
            'nombre' => "Semestre {$anio}-{$semestre}",
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'activo' => $semestre === 1,
        ];
    }

    public function activo(): static
    {
        return $this->state(fn () => ['activo' => true]);
    }
}
