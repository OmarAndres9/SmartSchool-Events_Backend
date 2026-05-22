<?php

namespace Database\Factories;

use App\Models\Eventos;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventoValoracionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'evento_id' => Eventos::factory(),
            'user_id' => User::factory(),
            'puntuacion' => fake()->numberBetween(1, 5),
            'comentario' => fake()->optional(70)->sentence(),
        ];
    }

    public function positiva(): static
    {
        return $this->state(fn () => ['puntuacion' => fake()->numberBetween(4, 5)]);
    }

    public function negativa(): static
    {
        return $this->state(fn () => ['puntuacion' => fake()->numberBetween(1, 2)]);
    }
}
