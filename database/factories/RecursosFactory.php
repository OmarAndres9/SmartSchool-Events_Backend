<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecursosFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement([
                'Auditorio Principal', 'Salón 101', 'Salón 102', 'Salón 201',
                'Laboratorio de Química', 'Laboratorio de Física',
                'Cancha de Fútbol', 'Cancha de Baloncesto',
                'Biblioteca', 'Sala de Cómputo', 'Salón de Música',
                'Salón de Danzas', 'Teatro', 'Cafetería',
                'Aula Virtual Zoom', 'Plataforma Meet',
            ]),
            'tipo' => fake()->randomElement([
                'salon', 'auditorio', 'laboratorio', 'cancha',
                'biblioteca', 'salon_computo', 'virtual',
                'area_exterior', 'teatro',
            ]),
            'ubicacion' => fake()->randomElement([
                'Bloque A', 'Bloque B', 'Bloque C', 'Edificio Principal',
                'Sede Norte', 'Sede Sur', 'Virtual',
            ]),
            'capacidad' => fake()->optional(80)->numberBetween(10, 500),
            'estado' => fake()->randomElement([
                'disponible', 'ocupado', 'mantenimiento',
            ]),
            'descripcion' => fake()->optional()->sentence(),
        ];
    }

    public function disponible(): static
    {
        return $this->state(fn () => ['estado' => 'disponible']);
    }
}
