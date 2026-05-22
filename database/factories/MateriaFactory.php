<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MateriaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement([
                'Matemáticas', 'Lenguaje', 'Ciencias Naturales', 'Inglés',
                'Historia', 'Educación Física', 'Artes', 'Filosofía',
                'Química', 'Física', 'Biología', 'Geografía',
                'Ética', 'Religión', 'Tecnología', 'Informática',
            ]),
            'descripcion' => fake()->sentence(),
            'docente_id' => User::factory(),
        ];
    }
}
