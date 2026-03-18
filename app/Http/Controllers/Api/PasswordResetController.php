<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * POST /api/v1/password/email
     * Envía el link de recuperación al correo del usuario.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No encontramos una cuenta con ese correo electrónico.',
        ]);

        // FIX: usar broker 'users' explícitamente
        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Te enviamos un enlace de recuperación a tu correo. Revisa tu bandeja de entrada.',
            ], 200);
        }

        return response()->json([
            'message' => 'No se pudo enviar el correo. Intenta de nuevo en unos minutos.',
        ], 400);
    }

    /**
     * POST /api/v1/password/reset
     * Resetea la contraseña usando el token recibido por email.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // FIX: usar broker 'users' explícitamente
        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => '¡Contraseña actualizada exitosamente! Ya puedes iniciar sesión.',
            ], 200);
        }

        return response()->json([
            'message' => 'El enlace es inválido o ha expirado. Solicita uno nuevo.',
        ], 400);
    }
}
