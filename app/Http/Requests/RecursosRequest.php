<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecursosRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'      => 'required|string|max:255',
            'tipo'        => 'nullable|string|max:255',
            'ubicacion'   => 'required|string|max:255',
            'capacidad'   => 'nullable|integer|min:1',
            'estado'      => 'required|string|in:disponible,ocupado,mantenimiento',
            'descripcion' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser: disponible, ocupado o mantenimiento.',
        ];
    }
}
