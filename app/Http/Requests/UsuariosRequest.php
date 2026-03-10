<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuariosRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email'.($userId ? ','.$userId : ''),
            'password' => $userId ? 'sometimes|string|min:8' : 'required|string|min:8',
            'documento' => 'required|string|max:12|unique:users,documento'.($userId ? ','.$userId : ''),
            'tipo_documento' => 'required|string|max:255',
        ];
    }
}
