<?php

namespace Database\Factories;

use App\Models\Eventos;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReporteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo' => fake()->randomElement([
                'asistencia', 'rendimiento', 'evento', 'incidente', 'general',
            ]),
            'descripcion' => fake()->paragraph(),
            'fecha' => fake()->dateTimeBetween('-2 months', 'now'),
            'estado' => fake()->randomElement(['pendiente', 'aprobado', 'rechazado']),
            'id_usuario' => User::factory(),
            'id_evento' => fake()->optional(50)->randomElement([Eventos::factory()]),
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn () => ['estado' => 'pendiente']);
    }
}
