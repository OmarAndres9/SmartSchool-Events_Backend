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
            'nombre'               => 'required|string|max:255',
            'descripcion'          => 'nullable|string',
            'fecha_inicio'         => 'required|date',
            'fecha_fin'            => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar'                => 'nullable|string|max:255',
            'tipo_evento'          => 'required|string|in:Academico,Cultural,Deportivo,Recreativo',
            'modalidad'            => 'required|string|in:Presencial,Virtual,Mixta',
            'grupo_destinado'      => 'nullable|string|max:255',
            'es_recurrente'        => 'boolean',
            'tipo_recurrencia'     => 'required_if:es_recurrente,true|in:diario,semanal,quincenal,mensual,anualmente',
            'intervalo'            => 'nullable|integer|min:1',
            'dias_semana'          => 'nullable|array',
            'dias_semana.*'        => 'string|in:L,M,MX,J,V,S,D',
            'fecha_fin_recurrencia' => 'nullable|date|after_or_equal:fecha_inicio',
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
