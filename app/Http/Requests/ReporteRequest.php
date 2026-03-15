<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo'        => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha'       => 'required|date',
            'estado'      => 'required|string|in:activo,pendiente,finalizado,cancelado',
            // FIX: id_usuario era required desde el frontend — ahora el controller lo inyecta
            'id_usuario'  => 'sometimes|exists:users,id',
            'id_evento'   => 'nullable|exists:eventos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser: activo, pendiente, finalizado o cancelado.',
        ];
    }
}
