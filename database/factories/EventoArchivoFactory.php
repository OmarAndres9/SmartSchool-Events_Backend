<?php

namespace Database\Factories;

use App\Models\Eventos;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventoArchivoFactory extends Factory
{
    public function definition(): array
    {
        $ext = fake()->randomElement(['pdf', 'docx', 'pptx', 'jpg', 'png', 'xlsx']);
        return [
            'evento_id' => Eventos::factory(),
            'nombre_original' => fake()->word() . '.' . $ext,
            'ruta' => 'eventos/' . fake()->uuid() . '.' . $ext,
            'tipo' => fake()->mimeType(),
            'tamano' => fake()->numberBetween(1024, 5 * 1024 * 1024),
        ];
    }
}
