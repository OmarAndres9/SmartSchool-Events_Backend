<?php

namespace Database\Factories;

use App\Models\Eventos;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificacionesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titulo' => fake()->randomElement([
                'Nuevo evento creado', 'Recordatorio de evento',
                'Cambio de horario', 'Cancelación de evento',
                'Inscripción confirmada', 'Nueva calificación',
                'Reunión de padres', 'Evento próximo',
            ]),
            'mensaje' => fake()->paragraph(),
            'tipo' => fake()->randomElement([
                'informacion', 'recordatorio', 'alerta', 'confirmacion',
            ]),
            'canal' => fake()->randomElement(['email', 'notificacion', 'ambos']),
            'fecha_creacion' => fake()->dateTimeBetween('-1 month', 'now'),
            'id_usuario' => User::factory(),
            'id_evento' => fake()->optional(60)->randomElement([Eventos::factory()]),
        ];
    }
}
