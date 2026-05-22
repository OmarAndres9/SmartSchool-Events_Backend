<?php

namespace App\Console\Commands;

use App\Models\Eventos;
use App\Models\User;
use App\Notifications\EventoCreada;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EnviarRecordatoriosEventos extends Command
{
    protected $signature = 'eventos:enviar-recordatorios';
    protected $description = 'Envía recordatorios de eventos próximos a estudiantes inscritos';

    public function handle(): int
    {
        $now = now();
        $enviados = 0;

        Eventos::whereNull('evento_origen_id')
            ->where('fecha_inicio', '>', $now)
            ->where('fecha_inicio', '<', $now->copy()->addDays(7))
            ->chunk(50, function ($eventos) use ($now, &$enviados) {
                foreach ($eventos as $evento) {
                    $inscritos = $evento->inscripciones()
                        ->wherePivot('estado', '!=', 'cancelada')
                        ->get();

                    foreach ($inscritos as $user) {
                        if (! $user->recordatorio_email) continue;

                        $anticipacion = $user->recordatorio_anticipacion_minutos;
                        $horaRecordatorio = $evento->fecha_inicio->copy()->subMinutes($anticipacion);

                        if ($now->greaterThanOrEqualTo($horaRecordatorio)) {
                            $user->notify(new EventoCreada($evento, 'recordatorio'));
                            $enviados++;
                        }
                    }
                }
            });

        $this->info("Recordatorios enviados: {$enviados}");
        return Command::SUCCESS;
    }
}
