<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Link al frontend React (config('app.frontend_url')) para resetear contraseña.
     */
    public function toMail($notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url');

        $url = $frontendUrl . '/reset-password?token=' . $this->token
             . '&email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Recuperar contraseña — SmartSchool Events')
            ->greeting('Hola 👋')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este correo.')
            ->salutation('SmartSchool Events');
    }
}
