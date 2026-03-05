<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

/**
 * CAPA: Service (Application Service Layer)
 *
 * MailService tiene UNA sola responsabilidad: enviar correos.
 * Encapsula toda la configuración y uso de PHPMailer.
 *
 * Principio aplicado: Single Responsibility Principle (SRP).
 *
 * Al inyectarlo en otros servicios, el resto de la aplicación
 * no necesita saber CÓMO se envían los correos, solo llama sendPasswordReset().
 *
 * Configuración SMTP: se lee desde config/mail.php que a su vez
 * lee el .env — nunca hardcodeamos credenciales aquí.
 */
class MailService
{
    protected PHPMailer $mailer;

    public function __construct()
    {
        // true = activar excepciones (en lugar de retornar false silenciosamente)
        $this->mailer = new PHPMailer(true);

        // ── Configuración del servidor SMTP ──────────────────────────────────

        // Usar transporte SMTP (en vez del mail() de PHP)
        $this->mailer->isSMTP();

        // Host del servidor (ej: smtp.gmail.com)
        $this->mailer->Host = config('mail.mailers.smtp.host');

        // Activar autenticación con usuario y contraseña
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = config('mail.mailers.smtp.username');
        $this->mailer->Password = config('mail.mailers.smtp.password');

        // Tipo de cifrado: STARTTLS (puerto 587) o SMTPS (puerto 465)
        $encryption = config('mail.mailers.smtp.encryption');
        $this->mailer->SMTPSecure = ($encryption === 'ssl')
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;

        // Puerto SMTP (587 para TLS, 465 para SSL)
        $this->mailer->Port = config('mail.mailers.smtp.port');

        // Charset UTF-8 para soportar tildes y caracteres especiales
        $this->mailer->CharSet = 'UTF-8';

        // Remitente del correo (el "De:" que ve el destinatario)
        $this->mailer->setFrom(
            config('mail.from.address'),
            config('mail.from.name')
        );
    }

    /**
     * Envía el correo de recuperación de contraseña.
     *
     * @param string $toEmail  Email del destinatario
     * @param string $toName   Nombre del destinatario (para personalizar el correo)
     * @param string $resetUrl URL completa con el token para resetear la contraseña
     *
     * @throws \RuntimeException Si el envío falla
     */
    public function sendPasswordReset(string $toEmail, string $toName, string $resetUrl): void
    {
        try {
            // Limpiar destinatarios anteriores (por si el objeto se reutiliza)
            $this->mailer->clearAddresses();

            // Agregar destinatario
            $this->mailer->addAddress($toEmail, $toName);

            // Activar modo HTML
            $this->mailer->isHTML(true);

            // Asunto del correo
            $this->mailer->Subject = 'Recuperación de contraseña — Sistema RRHH';

            // Cuerpo HTML (visual, para clientes que soportan HTML)
            $this->mailer->Body = $this->buildResetEmailHtml($toName, $resetUrl);

            // Cuerpo de texto plano (fallback para clientes que no soportan HTML)
            $this->mailer->AltBody =
                "Hola $toName, usa este enlace para restablecer tu contraseña: " .
                "$resetUrl (Expira en 30 minutos). " .
                "Si no solicitaste este cambio, ignora este correo.";

            $this->mailer->send();

        } catch (MailerException $e) {
            // Relanzamos como RuntimeException genérica para no exponer
            // detalles de PHPMailer fuera de este servicio
            throw new \RuntimeException(
                'Error al enviar el correo: ' . $this->mailer->ErrorInfo
            );
        }
    }

    /**
     * Genera el HTML del correo de recuperación.
     * Separado en su propio método para mantener sendPasswordReset() limpio.
     *
     * @param string $nombre Nombre del usuario
     * @param string $url    URL de reset
     * @return string HTML del correo
     */
    private function buildResetEmailHtml(string $nombre, string $url): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin:0; padding:0; background-color:#0d0d0d; font-family: 'Georgia', serif;">

            <table width="100%" cellpadding="0" cellspacing="0" style="padding: 48px 16px;">
                <tr>
                    <td align="center">
                        <table width="520" cellpadding="0" cellspacing="0"
                               style="background:#111111; border:1px solid #2a2a2a; border-radius:4px; overflow:hidden;">

                            <!-- Barra superior decorativa -->
                            <tr>
                                <td style="background: linear-gradient(90deg, #C8A96E 0%, #E8C97E 50%, #C8A96E 100%);
                                           height:3px; font-size:0; line-height:0;">
                                    &nbsp;
                                </td>
                            </tr>

                            <!-- Header -->
                            <tr>
                                <td style="padding: 36px 48px 28px; border-bottom: 1px solid #1e1e1e;">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>
                                                <p style="margin:0 0 4px; color:#C8A96E; font-size:10px;
                                                          letter-spacing:3px; text-transform:uppercase;
                                                          font-family: 'Arial', sans-serif;">
                                                    SISTEMA RRHH
                                                </p>
                                                <h1 style="margin:0; color:#f0ece4; font-size:24px;
                                                           font-weight:normal; letter-spacing:-0.3px;
                                                           font-family: 'Georgia', serif;">
                                                    Acceso seguro
                                                </h1>
                                            </td>
                                            <td align="right" valign="middle">
                                                <div style="width:42px; height:42px; background:#1a1a1a;
                                                            border:1px solid #2a2a2a; border-radius:50%;
                                                            text-align:center; line-height:42px; font-size:18px;">
                                                    🔑
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Body -->
                            <tr>
                                <td style="padding: 36px 48px;">

                                    <!-- Saludo -->
                                    <p style="margin:0 0 6px; color:#888; font-size:11px;
                                              letter-spacing:2px; text-transform:uppercase;
                                              font-family:'Arial', sans-serif;">
                                        PARA
                                    </p>
                                    <p style="margin:0 0 28px; color:#f0ece4; font-size:20px;
                                              font-family:'Georgia', serif; font-weight:normal;">
                                        {$nombre}
                                    </p>

                                    <!-- Divisor -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                        <tr>
                                            <td style="border-top:1px solid #1e1e1e;">&nbsp;</td>
                                        </tr>
                                    </table>

                                    <!-- Mensaje -->
                                    <p style="margin:0 0 24px; color:#a09a90; font-size:15px;
                                              line-height:1.75; font-family:'Georgia', serif;">
                                        Recibimos una solicitud para restablecer la contraseña
                                        de tu cuenta. Si fuiste tú, usa el enlace a continuación
                                        para continuar el proceso.
                                    </p>

                                    <!-- Botón CTA -->
                                    <table cellpadding="0" cellspacing="0" style="margin: 32px 0;">
                                        <tr>
                                            <td style="background:#C8A96E; border-radius:2px;">
                                                <a href="{$url}"
                                                   style="display:inline-block; color:#0d0d0d;
                                                          padding:14px 32px; font-size:12px;
                                                          font-weight:bold; text-decoration:none;
                                                          letter-spacing:2px; text-transform:uppercase;
                                                          font-family:'Arial', sans-serif;">
                                                    Restablecer contraseña →
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- URL de respaldo -->
                                    <p style="margin:0 0 6px; color:#555; font-size:11px;
                                              letter-spacing:1.5px; text-transform:uppercase;
                                              font-family:'Arial', sans-serif;">
                                        O COPIA ESTE ENLACE
                                    </p>
                                    <p style="margin:0 0 28px; color:#555; font-size:12px;
                                              word-break:break-all; font-family:'Courier New', monospace;
                                              background:#0a0a0a; padding:12px 16px;
                                              border-left:2px solid #C8A96E; border-radius:0 2px 2px 0;">
                                        {$url}
                                    </p>

                                    <!-- Aviso de expiración -->
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="background:#161616; border:1px solid #1e1e1e;
                                                  border-radius:2px; margin-top:8px;">
                                        <tr>
                                            <td style="padding:14px 18px;">
                                                <p style="margin:0; color:#888; font-size:12px;
                                                          line-height:1.6; font-family:'Arial', sans-serif;">
                                                    ⏱ &nbsp;Este enlace expira en
                                                    <span style="color:#C8A96E; font-weight:bold;">30 minutos</span>.
                                                    Si no solicitaste este cambio, ignora este correo —
                                                    tu contraseña permanecerá sin cambios.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>

                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style="padding:20px 48px; border-top:1px solid #1a1a1a;">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>
                                                <p style="margin:0; color:#3a3a3a; font-size:11px;
                                                          font-family:'Arial', sans-serif; letter-spacing:0.5px;">
                                                    Correo automático · No responder
                                                </p>
                                            </td>
                                            <td align="right">
                                                <p style="margin:0; color:#3a3a3a; font-size:11px;
                                                          font-family:'Arial', sans-serif;">
                                                    © Sistema SmartSchool Events
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Barra inferior decorativa -->
                            <tr>
                                <td style="background: linear-gradient(90deg, #C8A96E 0%, #E8C97E 50%, #C8A96E 100%);
                                           height:2px; font-size:0; line-height:0;">
                                    &nbsp;
                                </td>
                            </tr>

                        </table>

                        <!-- Nota bajo el card -->
                        <p style="margin:20px 0 0; color:#333; font-size:11px;
                                  font-family:'Arial', sans-serif; letter-spacing:0.5px;">
                            Enviado de forma segura por Sistema SmartSchool Events
                        </p>

                    </td>
                </tr>
            </table>

        </body>
        </html>
        HTML;
    }
}