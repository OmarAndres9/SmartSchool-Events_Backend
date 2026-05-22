<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventosFactory extends Factory
{
    public function definition(): array
    {
        $fechaInicio = fake()->dateTimeBetween('-1 month', '+3 months');
        $fin = (clone $fechaInicio)->modify('+' . fake()->numberBetween(1, 8) . ' hours');

        return [
            'nombre' => fake()->randomElement([
                'Taller de Liderazgo', 'Conferencia de Ciencias',
                'Reunión de Padres', 'Feria Cultural',
                'Campeonato Deportivo', 'Obra de Teatro',
                'Charla Motivacional', 'Día del Estudiante',
                'Exposición de Arte', 'Jornada de Limpieza',
                'Concurso de Oratoria', 'Festival de Música',
                'Feria de la Ciencia', 'Reunión de Docentes',
                'Taller de Lectura', 'Olimpiadas Matemáticas',
            ]),
            'descripcion' => fake()->optional()->paragraph(),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => fake()->boolean(70) ? $fin : null,
            'lugar' => fake()->optional(80)->randomElement([
                'Auditorio Principal', 'Salón 101', 'Salón 203',
                'Cancha Deportiva', 'Biblioteca', 'Laboratorio',
                'Salón de Actos', 'Patio Central', 'Aula Múltiple',
            ]),
            'tipo_evento' => fake()->randomElement([
                'taller', 'conferencia', 'reunion', 'cultural',
                'deportivo', 'academico', 'social',
            ]),
            'modalidad' => fake()->randomElement(['presencial', 'virtual', 'mixto']),
            'grupo_destinado' => fake()->optional()->randomElement([
                'Todos', 'Estudiantes', 'Docentes', 'Padres de Familia',
                'Primaria', 'Secundaria', 'Bachillerato',
            ]),
            'creado_por' => User::factory(),
            'es_recurrente' => false,
            'visibilidad' => fake()->randomElement(['publico', 'privado']),
        ];
    }

    public function recurrente(): static
    {
        return $this->state(fn () => [
            'es_recurrente' => true,
            'tipo_recurrencia' => fake()->randomElement(['diaria', 'semanal', 'mensual']),
            'intervalo' => 1,
            'fecha_fin_recurrencia' => fake()->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
        ]);
    }

    public function publico(): static
    {
        return $this->state(fn () => ['visibilidad' => 'publico']);
    }

    public function privado(): static
    {
        return $this->state(fn () => ['visibilidad' => 'privado']);
    }
}
