<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificacionesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'         => 'required|string|max:255',
            'mensaje'        => 'required|string',
            'tipo'           => 'required|string|in:success,warning,danger,info',
            // FIX: canales alineados con el frontend (Sistema, Email, WhatsApp, SMS)
            // Se aceptan en minúsculas o con mayúsculas para mayor compatibilidad
            'canal'          => 'required|string|in:Sistema,Email,WhatsApp,SMS,email,sms,app,push',
            'fecha_creacion' => 'sometimes|date',
            'id_usuario'     => 'sometimes|exists:users,id',
            'id_evento'      => 'nullable|exists:eventos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.in'  => 'El tipo debe ser: success, warning, danger o info.',
            'canal.in' => 'El canal debe ser: Sistema, Email, WhatsApp o SMS.',
        ];
    }
}
