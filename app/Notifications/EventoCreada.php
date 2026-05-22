<?php

namespace App\Notifications;

use App\Models\Eventos;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventoCreada extends Notification
{
    use Queueable;

    public function __construct(
        public Eventos $evento,
        public string $accion = 'creado'
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url');

        return (new MailMessage)
            ->subject("Evento {$this->accion}: {$this->evento->nombre}")
            ->greeting('Hola 👋')
            ->line("El evento **{$this->evento->nombre}** ha sido {$this->accion}.")
            ->line("Fecha: {$this->evento->fecha_inicio}")
            ->line("Tipo: {$this->evento->tipo_evento} | Modalidad: {$this->evento->modalidad}")
            ->action('Ver evento', "{$frontendUrl}/eventos/{$this->evento->id}")
            ->salutation('SmartSchool Events');
    }

    public function toArray($notifiable): array
    {
        return [
            'evento_id' => $this->evento->id,
            'nombre'    => $this->evento->nombre,
            'accion'    => $this->accion,
        ];
    }
}
