<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'solicitante_id' => User::factory(),
            'destinatario_id' => User::factory(),
            'fecha_solicitada' => fake()->dateTimeBetween('now', '+1 month'),
            'motivo' => fake()->randomElement([
                'Solicitud de reunión', 'Entrega de notas',
                'Problema académico', 'Orientación vocacional',
                'Queja o sugerencia', 'Seguimiento académico',
                'Otros',
            ]),
            'comentario' => fake()->optional(60)->sentence(),
            'estado' => fake()->randomElement([
                'pendiente', 'aprobada', 'rechazada', 'completada', 'cancelada',
            ]),
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn () => ['estado' => 'pendiente']);
    }

    public function aprobada(): static
    {
        return $this->state(fn () => ['estado' => 'aprobada']);
    }
}
