<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->routeIs('*register')) {
            return [
                'name'           => 'required|string|max:255',
                'email'          => 'required|string|email|max:255|unique:users',
                'password'       => 'required|string|min:6|confirmed',
                'documento'      => 'required|string|max:12|unique:users',
                'tipo_documento' => 'required|string|max:10',
                'rol'            => 'required|string',
            ];
        }

        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es requerido.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',

            'email.required' => 'El campo correo es requerido.',
            'email.email' => 'El correo debe ser válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'email.max' => 'El correo no puede exceder 255 caracteres.',

            'password.required' => 'El campo contraseña es requerido.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.min' => 'La contraseña debe tener mínimo 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            'documento.required' => 'El campo documento es requerido.',
            'documento.string' => 'El documento debe ser texto.',
            'documento.max' => 'El documento no puede exceder 12 caracteres.',
            'documento.unique' => 'Este documento ya está registrado.',

            'tipo_documento.required' => 'El tipo de documento es requerido.',
            'tipo_documento.string' => 'El tipo de documento debe ser texto.',
            'tipo_documento.max' => 'El tipo de documento no puede exceder 10 caracteres.',

            'rol.required' => 'El rol es requerido.',
            'rol.string' => 'El rol debe ser texto.',
        ];
    }
}

   