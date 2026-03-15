<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'fecha_inicio'    => 'required|date',
            // FIX: fecha_fin era required pero en el form es opcional
            'fecha_fin'       => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar'           => 'nullable|string|max:255',
            'tipo_evento'     => 'required|string|in:Academico,Cultural,Deportivo,Recreativo',
            'modalidad'       => 'required|string|in:Presencial,Virtual,Mixta',
            // FIX: grupo_destinado era required pero es opcional semánticamente
            'grupo_destinado' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'       => 'El nombre del evento es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date'     => 'La fecha de inicio no es válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior al inicio.',
            'tipo_evento.in'        => 'El tipo de evento no es válido.',
            'modalidad.in'          => 'La modalidad no es válida.',
        ];
    }
}
