<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name'                           => 'sometimes|string|max:255',
            'email'                          => 'sometimes|email|max:255|unique:users,email,' . $userId,
            'password'                       => 'sometimes|string|min:8|confirmed',
            'recordatorio_email'             => 'boolean',
            'recordatorio_anticipacion_minutos' => 'integer|min:15|max:10080',
        ];
    }
}
