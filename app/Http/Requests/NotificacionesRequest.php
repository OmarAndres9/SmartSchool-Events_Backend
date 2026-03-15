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
            'canal'          => 'required|string|in:email,sms,app,push',
            // FIX: estos los inyecta el controller automáticamente
            'fecha_creacion' => 'sometimes|date',
            'id_usuario'     => 'sometimes|exists:users,id',
            'id_evento'      => 'nullable|exists:eventos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.in'  => 'El tipo debe ser: success, warning, danger o info.',
            'canal.in' => 'El canal debe ser: email, sms, app o push.',
        ];
    }
}
