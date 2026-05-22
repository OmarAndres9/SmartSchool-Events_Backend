<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destinatario_id' => 'required|exists:users,id',
            'fecha_solicitada' => 'required|date|after:now',
            'motivo'           => 'required|string|max:255',
            'comentario'       => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'destinatario_id.required'    => 'El destinatario es obligatorio.',
            'destinatario_id.exists'      => 'El destinatario no existe.',
            'fecha_solicitada.required'   => 'La fecha de la cita es obligatoria.',
            'fecha_solicitada.after'      => 'La fecha debe ser posterior a ahora.',
            'motivo.required'             => 'El motivo es obligatorio.',
            'motivo.max'                  => 'El motivo no debe exceder 255 caracteres.',
        ];
    }
}
